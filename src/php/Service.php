<?php

namespace jlgtechnology;

use jlgtechnology\model\{
    File as ModelFile,
    Person as ModelPerson,
    Company as ModelCompany,
    Loan as ModelLoan
};

use GuzzleHttp\{
    Client as GuzzleClient,
    RequestOptions as GuzzleRequestOptions,
    RedirectMiddleware as GuzzleRedirectMiddleware
};

use Psr\Http\Message\ResponseInterface;

use GuzzleHttp\Exception\{
    ClientException,
    ServerException
};

use \Exception as Exception;

class Service
{
    public  const CRM_AUTH_ENDPOINT = 
        "https://auth.just-cashflow.com/oauth/token";
    public  const CRM_API_URL       = "https://api.crm.prod.jlg-technology.com";
    private const CRM_DEV_API_URL   = "https://api.crm.dev.jlg-technology.com";

    private const DATE_TIME_FORMAT = "Y-m-d H:i:s";

    private static $_boolDebugOn;
    private static $_guzzleClient;

    private $_strJWT;

    private function __construct(string $strJWT)
    {
        $this->_strJWT = $strJWT;
    }

    /**
     * Used to create the API service if you already have a token
     */
    public static function createFromToken(string $strJWT) : self
    {
        return new self($strJWT);
    }

    /**
     * Used to create the API service if you need to also generate a token
     */
    public static function createFromCredentials(
        string $strClientId,
        string $strClientSecret
    ) : self
    {
        $arrHeaders = [
            "Content-Type" => "application/json",
            "Accept" => "application/json"
        ];

        $arrData = [
            "grant_type"    => "client_credentials",
            "client_id"     => $strClientId,
            "client_secret" => $strClientSecret,
            "audience"      => self::_getURLHost()
        ];

        /**
         * Create the guzzle options
         */
        $arrOptions = [
            GuzzleRequestOptions::HEADERS => $arrHeaders,
            GuzzleRequestOptions::JSON    => $arrData
        ];

        try {
            $guzzleResponse = self::_getGuzzleClient()->request(
                "POST",
                self::CRM_AUTH_ENDPOINT,
                $arrOptions
            );

        } catch (ClientException $ex) {
            $strReasonPhrase = "No response returned";
            $intStatusCode = 400;

            $guzzleResponse = $ex->getResponse();
            if (!is_null($guzzleResponse)) {
                $strReasonPhrase = 
                    $guzzleResponse->getReasonPhrase() . ", " . 
                    $guzzleResponse->getBody()->getContents();
                $intStatusCode   = $guzzleResponse->getStatusCode();
            }

            throw new Exception(
                sprintf(
                    "Client error returned from %s (%s)",
                    self::CRM_AUTH_ENDPOINT,
                    $strReasonPhrase
                ), 
                $intStatusCode
            );

        } catch (ServerException $ex) {
            $strReasonPhrase = "No response returned";
            $intStatusCode = 500;

            $guzzleResponse = $ex->getResponse();
            if (!is_null($guzzleResponse)) {
                $strReasonPhrase = 
                    $guzzleResponse->getReasonPhrase() . ", " . 
                    $guzzleResponse->getBody()->getContents();
                $intStatusCode   = $guzzleResponse->getStatusCode();
            }

            throw new Exception(
                sprintf(
                    "Server error returned from %s (%s)",
                    self::CRM_AUTH_ENDPOINT,
                    $strReasonPhrase
                ), 
                $intStatusCode
            );
        }

        /**
         * If no contents were returned in the body then return null
         */
        $objBody = $guzzleResponse->getBody();
        if (!$objBody->isReadable()) {
            throw new Exception(
                "Contents returned from authentication server aren't readable"
            );
        }

        /**
         * Else the contents should always be in json format
         */
        $arrResponse = json_decode($objBody->getContents(), true);

        /**
         * Check we were able to decode the results properly
         */
        if (
            $arrResponse === false ||
            !array_key_exists("access_token", $arrResponse)
        ) {
            throw new Exception(
                "Unable to decode results from server",
                415
            );
        }
        return new self($arrResponse["access_token"]);
    }

    /**
     * Will change the active API URL to the developer CRM API URL
     */
    public static function setDebugMode(bool $boolDebugOn)
    {
        self::$_boolDebugOn = $boolDebugOn;
    }

    /**
     * Injects a guzzle client to be used for all requests in order to unit
     * test the service or see what requests are being made
     */
    public static function setGuzzleClientForUnitTests(
        GuzzleClient $guzzleClient
    )
    {
        self::$_guzzleClient = $guzzleClient;
    }

    /**
     * Returns the API URL dependent on the debug mode variable
     */
    private static function _getURLHost() : string
    {
        return self::$_boolDebugOn 
            ? self::CRM_DEV_API_URL 
            : self::CRM_API_URL;
    }

    /**
     * Gets the guzzle client or creates one if it doesn't exist
     */
    private static function _getGuzzleClient() : GuzzleClient
    {
        if (!isset(self::$_guzzleClient)) {
            self::$_guzzleClient = new GuzzleClient();
        }

        return self::$_guzzleClient;
    }

    /**
     * Gets the token used by this instance of the service
     */
    public function getToken() : string
    {
        return $this->_strJWT;
    }

    /**
     * Uploads files and creates a case for the models provided
     */
    public function createApplication(
        ModelCompany $modelPrimaryCompany,
        ModelLoan $modelLoan,
        array $arrApplicants
    ) : int
    {
        /**
         * Validate the array of applicants are ModelPersons and ModelCompanys
         */
        $arrModelPersons = [];
        $arrModelCompanies = [];
        foreach ($arrApplicants as $key => $modelApplicant) {
            if ($modelApplicant instanceof ModelPerson) {
                $arrModelPersons[] = $modelApplicant;

            } else if ($modelApplicant instanceof ModelCompany) {
                $arrModelCompanies[] = $modelApplicant;

            } else {
                throw new Exception(
                    "An element of the applicant array is not a " . 
                        "person or company model - " . 
                        "element is at positon $key with value " . 
                        print_r($modelApplicant, true)
                );
            }
        }

        /**
         * Get all the files and upload them
         */
        $arrModelFiles = [];
        foreach ($modelPrimaryCompany->getFiles() as $modelFile) {
            $arrModelFiles[] = $modelFile;
        }
        foreach ($modelLoan->getFiles() as $modelFile) {
            $arrModelFiles[] = $modelFile;
        }
        foreach ($arrModelPersons as $modelPerson) {
            foreach ($modelPerson->getFiles() as $modelFile) {
                $arrModelFiles[] = $modelFile;
            }
        }
        foreach ($arrModelCompanies as $modelCompany) {
            foreach ($modelCompany->getFiles() as $modelFile) {
                $arrModelFiles[] = $modelFile;
            }
        }

        /**
         * Upload post will upload the files to a temporary dropbox and set
         * the upload path in the file models
         */
        $this->_uploadPost($arrModelFiles);

        /**
         * Create the case
         */
        $intCasePK = $this->_casePost(
            $modelPrimaryCompany,
            $modelLoan,
            $arrModelPersons,
            $arrModelCompanies
        );

        return $intCasePK;
    }

    /**
     * Returns a presigned url to a file from the dropbox bucket
     */
    public function getUploadedFilePresignedUrl(string $strUploadPath)
    {
        /**
         * Validate that a non empty string was given
         */
        if (
            $strUploadPath === "" ||
            strpos($strUploadPath, ' ') !== false
        ) {
            throw new Exception(
                "Invalid upload path provided - is $strUploadPath"
            );
        }

        return $this->_uploadGet($strUploadPath);
    }

    /**
     * Calls /upload POST
     */
    private function _uploadPost(array $arrModelFiles)
    {
        /**
         * If there are no files then don't bother making a request
         */
        if ($arrModelFiles === []) {
            return;
        }

        /**
         * Set the request parameters
         */
        $strPath = "/upload";

        $strMethod = "POST";

        $arrHeaders = [
            "Authorization" => $this->_strJWT,
            "Accept" => "application/json"
        ];

        /**
         * Format the model files into multipart form data
         */
        $arrMultipartFileData = [];
        foreach ($arrModelFiles as $key => $modelFile) {
            $arrMultipartFileData[] = [
                "name"     => strval($key),
                "contents" => fopen($modelFile->getNameAndPath(), "r"),
                "headers"  => [
                    "Content-Type" => $modelFile->getMimeType()
                ]
            ];
        }

        /**
         * Make the request to /upload and decode the results
         */
        $arrResponse = $this->_makeRequest(
            $strPath,
            $strMethod,
            $arrHeaders,
            $arrMultipartFileData
        );

        /**
         * Make sure every file in $arrModelFiles has a matching generated file
         * name in $arrResponse
         */
        if (
            is_null($arrResponse) ||
            array_diff_key($arrModelFiles, $arrResponse) !== [] ||
            array_diff_key($arrResponse, $arrModelFiles) !== []
        ) {
            throw new Exception(
                "Unknown error occured, " .
                    "mapping of files to generated paths cannot be created",
                415
            );
        }

        /**
         * Set the upload path for each file from the results
         */
        foreach ($arrModelFiles as $key => $modelFile) {
            $modelFile->setUploadPath($arrResponse[$key]);
        }
    }

    /**
     * Calls /upload GET
     */
    private function _uploadGet(string $strUploadPath)
    {
        $strPath   = "/upload?File=$strUploadPath";

        $strMethod = "GET";

        $arrHeaders = [
            "Authorization" => $this->_strJWT
        ];

        /**
         * The value returned by make request would be the file's raw data
         * but we want the presigned url so we want to get the guzzle response
         * from the request which'll hold the presigned url
         */
        $this->_makeRequest(
            $strPath,
            $strMethod,
            $arrHeaders,
            null,
            $guzzleResponse
        );

        /**
         * _makeRequest tracks redirects which guzzle places in the
         * GuzzleRedirectMiddleware::HISTORY_HEADER of the response
         */
        $arrRedirectedToUrls = $guzzleResponse->getHeader(
            GuzzleRedirectMiddleware::HISTORY_HEADER
        );
        return end($arrRedirectedToUrls);
    }

    /**
     * Calls /case POST
     */
    private function _casePost(
        ModelCompany $modelPrimaryCompany,
        ModelLoan $modelLoan,
        array $arrModelPersons,
        array $arrModelCompanies
    ) : int
    {
        /**
         * Format the primary company data
         */
        $arrPrimaryData = $this->_getCompanyData($modelPrimaryCompany);

        /**
         * Format the model files on the loan
         */
        $arrFiles = [];
        foreach ($modelLoan->getFiles() as $modelFile) {
            $arrFiles[] = $this->_getFileData($modelFile);
        }
        $arrLoanData = [
            "FacilityAmountRequested" => $modelLoan->getAmount(),
            "FacilityUse"             => $modelLoan->getUse(),
            "Files"                   => $arrFiles
        ];

        /**
         * Format the entity person data and the name of the primary contact
         */
        $arrEntityPersonData = [];
        $strPrimaryContactName = null;
        foreach ($arrModelPersons as $modelPerson) {
            /**
             * Format the model files on this person
             */
            $arrFiles = [];
            foreach ($modelPerson->getFiles() as $modelFile) {
                $arrFiles[] = $this->_getFileData($modelFile);
            }

            /**
             * Format the model person data
             */
            $arrEntityPersonData[] = [
                "Type"            => "Person",
                "Title"           => $modelPerson->getTitle(),
                "Forename"        => $modelPerson->getForename(),
                "MiddleName"      => $modelPerson->getMiddleName(),
                "Surname"         => $modelPerson->getSurname(),
                "DOB"             => $modelPerson
                    ->getDateOfBirth()
                    ->format(self::DATE_TIME_FORMAT),
                "AddressLine1"    => $modelPerson->getAddressLine1(),
                "AddressLine2"    => $modelPerson->getAddressLine2(),
                "AddressLine3"    => $modelPerson->getAddressLine3(),
                "AddressLine4"    => $modelPerson->getAddressLine4(),
                "AddressPostcode" => $modelPerson->getAddressPostcode(),
                "DayPhone"        => $modelPerson->getDayPhone(),
                "MobilePhone"     => $modelPerson->getMobilePhone(),
                "Email"           => $modelPerson->getEmail(),
                "Notes"           => $modelPerson->getNotes(),
                "Position"        => $modelPerson->getPosition(),
                "Gender"          => $modelPerson->getGender(),
                "Files"           => $arrFiles,
                "PassportForename"  => $modelPerson->getPassportForename(),
                "PassportMiddleName"=> $modelPerson->getPassportMiddleName(),
                "PassportSurname"   => $modelPerson->getPassportSurname()
            ];

            if ($modelPerson->getIsPrimaryContact()) {
                $strPrimaryContactName = $modelPerson->getForename() . " " . 
                    $modelPerson->getSurname();
            }
        }

        if (
            is_null($strPrimaryContactName) &&
            count($arrModelPersons) > 0
        ) {
            throw new Exception(
                "There must be a primary contact when there is at least " . 
                    "one person on a case"
            );
        }

        /**
         * Format the entity company data
         */
        $arrEntityCompanyData = [];
        foreach ($arrModelCompanies as $modelCompany) {
            
            $arrEntityCompanyData[] = array_merge(
                ["Type" => "Company"],
                $this->_getCompanyData($modelCompany)
            );
        }

        $strPath = "/case";

        $strMethod = "POST";

        $arrHeaders = [
            "Authorization" => $this->_strJWT,
            "Content-Type"  => "application/json",
            "Accept"        => "application/json"
        ];

        $arrData = [
            "Primary"  => $arrPrimaryData,
            "Loan"     => $arrLoanData,
            "Entities" => array_merge(
                $arrEntityPersonData,
                $arrEntityCompanyData
            ),
            "PrimaryContactName" => $strPrimaryContactName
        ];

        /**
         * Make a request to /case POST and decode the response
         */
        $arrResponse = $this->_makeRequest(
            $strPath,
            $strMethod,
            $arrHeaders,
            $arrData
        );

        /**
         * Make sure that a CasePK was returned from /case POST API
         */
        if (
            is_null($arrResponse) ||
            !array_key_exists("CasePK", $arrResponse)
        ) {
            throw new Exception(
                "No CasePK returned from /case API", 
                415
            );
        }

        /**
         * Return the case pk of the created case
         */
        return $arrResponse["CasePK"];
    }

    /**
     * Makes a request to the API and returns the response
     */
    private function _makeRequest(
        string $strPath,
        string $strMethod,
        array $arrHeaders = [],
        $mixedData = null,
        ResponseInterface &$guzzleResponse = null
    )
    {
        /**
         * Guzzle response is an output only parameter
         */
        $guzzleResponse = null;

        /**
         * Set up the options array
         * ALLOW_REDIRECTS: Allow them and track them in order to get the 
         *                  effective URL of redirects.
         *                  The tracked URLs will be found in the 
         *                  GuzzleRedirectMiddleware::HISTORY_HEADER header
         *                  in the response
         * 
         * HEADERS: The user defined headers sent to the API.
         * 
         * JSON/MULTIPART/FORM_PARAMS: The body of the request.
         */
        $arrOptions = [
            GuzzleRequestOptions::ALLOW_REDIRECTS => [
                'track_redirects' => true
            ]
        ];

        /**
         * Set the headers if they exist
         */
        if ($arrHeaders) {
            $arrOptions[GuzzleRequestOptions::HEADERS] = $arrHeaders;
        }

        /**
         * Set the data depending on the Content-Type
         */
        if ($mixedData) {
            switch ($arrHeaders["Content-Type"] ?? null) {
                case "application/json":
                    $arrOptions[GuzzleRequestOptions::JSON] = $mixedData;
                    break;
                default:
                    if (is_array($mixedData)) {
                        $arrOptions[GuzzleRequestOptions::MULTIPART] = 
                            $mixedData;
                    } else {
                        $arrOptions[GuzzleRequestOptions::FORM_PARAMS] = 
                            $mixedData;
                    }
                    break;
            }
        }

        /**
         * Get the guzzle client
         */
        $guzzleClient = self::_getGuzzleClient();

        /**
         * Make a request to the API and set the output 
         * parameter $guzzleResponse to the request's response
         */
        try {
            $guzzleResponse = $guzzleClient->request(
                $strMethod,
                self::_getURLHost() . $strPath,
                $arrOptions
            );

        } catch (ClientException $ex) {
            $strReasonPhrase = "No response returned";
            $intStatusCode = 400;

            $guzzleResponse = $ex->getResponse();
            if (!is_null($guzzleResponse)) {
                $strReasonPhrase = 
                    $guzzleResponse->getReasonPhrase() . ", " . 
                    $guzzleResponse->getBody()->getContents();
                $intStatusCode   = $guzzleResponse->getStatusCode();
            }

            throw new Exception(
                sprintf(
                    "Client error returned from %s (%s)",
                    self::_getURLHost() . $strPath,
                    $strReasonPhrase
                ), 
                $intStatusCode
            );

        } catch (ServerException $ex) {
            $strReasonPhrase = "No response returned";
            $intStatusCode = 500;

            $guzzleResponse = $ex->getResponse();
            if (!is_null($guzzleResponse)) {
                $strReasonPhrase = 
                    $guzzleResponse->getReasonPhrase() . ", " . 
                    $guzzleResponse->getBody()->getContents();
                $intStatusCode   = $guzzleResponse->getStatusCode();
            }

            throw new Exception(
                sprintf(
                    "Server error returned from %s (%s)",
                    self::_getURLHost() . $strPath,
                    $strReasonPhrase
                ), 
                $intStatusCode
            );
        }

        /**
         * If no contents were returned in the body then return null
         */
        $objBody = $guzzleResponse->getBody();
        if (!$objBody->isReadable()) {
            return null;
        }

        /**
         * Decode the results based on what the request is supposed to accept
         */
        switch ($arrHeaders["Accept"] ?? null) {
            case "application/json":
                /**
                 * Decode the results to an array
                 */
                $arrResponse = json_decode($objBody->getContents(), true);

                /**
                 * Check we were able to decode the results properly
                 */
                if ($arrResponse === false) {
                    throw new Exception(
                        "Unable to decode results from server",
                        415
                    );
                }

                return $arrResponse;
                break;
            default:
                /**
                 * By default just return the contents of the body
                 */
                return $objBody->getContents();
                break;
        }
    }

    private function _getCompanyData(ModelCompany $modelCompany) 
    {
        $arrFileData = [];
        foreach ($modelCompany->getFiles() as $modelFile) {
            $arrFileData[] = $this->_getFileData($modelFile);
        }

        return [
            "CompanyName"               => 
                $modelCompany->getName(),
            "LegalStatus"               => 
                $modelCompany->getLegalStatus(),
            "TradingAddressLine1"       => 
                $modelCompany->getTradingAddressLine1(),
            "TradingAddressLine2"       => 
                $modelCompany->getTradingAddressLine2(),
            "TradingAddressLine3"       => 
                $modelCompany->getTradingAddressLine3(),
            "TradingAddressLine4"       => 
                $modelCompany->getTradingAddressLine4(),
            "TradingAddressPostcode"    => 
                $modelCompany->getTradingAddressPostcode(),    
            "RegisteredAddressLine1"    => 
                $modelCompany->getRegisteredAddressLine1(),    
            "RegisteredAddressLine2"    => 
                $modelCompany->getRegisteredAddressLine2(),    
            "RegisteredAddressLine3"    => 
                $modelCompany->getRegisteredAddressLine3(),    
            "RegisteredAddressLine4"    => 
                $modelCompany->getRegisteredAddressLine4(),    
            "RegisteredAddressPostcode" => 
                $modelCompany->getRegisteredAddressPostcode(),    
            "Telephone"                 => 
                $modelCompany->getTelephone(),
            "Email"                     => 
                $modelCompany->getEmail(),
            "Website"                   => 
                $modelCompany->getWebsite(),
            "Notes"                     => 
                $modelCompany->getNotes(),
            "IncorporationDate"         => 
                $modelCompany
                    ->getIncorporationDate()
                    ->format(self::DATE_TIME_FORMAT),
            "CompanyRegistrationNo"     => 
                $modelCompany->getCompanyRegistrationNumber(),
            "SicCodes"                  => 
                $modelCompany->getSicCodes(),
            "Position"                  =>
                $modelCompany->getPosition(),
            "Files"                     => 
                $arrFileData
        ];
    }

    private function _getFileData(ModelFile $modelFile)
    {
        return [
            "FileName"          => pathinfo(
                $modelFile->getNameAndPath(), 
                PATHINFO_FILENAME
            ),
            "GeneratedFileName" => $modelFile->getUploadPath(),
            "Description"       => $modelFile->getDescription(),
            "CategoryID"        => $modelFile->getCategoryId(),
            "MimeType"          => $modelFile->getMimeType()
        ];
    }
}