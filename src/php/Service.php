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
    RequestOptions
};

use Psr\Http\Message\ResponseInterface;

use GuzzleHttp\Exception\{
    ClientException,
    ServerException
};

use \Exception as Exception;

class Service
{
    private const CRM_AUTH_ENDPOINT = 
        "https://auth.just-cashflow.com/oauth/token";
    private const CRM_API_URL = "https://api.crm.dev.jlg-technology.com";

    private $_strJWT;

    private function __construct(string $strJWT)
    {
        $this->_strJWT = $strJWT;
    }

    public static function createFromToken($strJWT) : self
    {
        return new self($strJWT);
    }

    public static function createFromCredentials(
        string $strClientId,
        string $strSecret
    ) : self
    {
        $strUrl = self::CRM_AUTH_ENDPOINT;

        $strMethod = "POST";

        $arrHeaders = [
            "Content-Type" => "application/json"
        ];

        $arrData = [
            "grant_type"    => "client_credentials",
            "client_id"     => $strClientId,
            "client_secret" => $strSecret,
            "audience"      => self::CRM_API_URL
        ];

        $guzzleResponse = self::_makeRequest(
            $strUrl,
            $strMethod,
            $arrHeaders,
            $arrData
        );

        $arrResponse = self::_decodeJSONResponse($guzzleResponse);

        return new self($arrResponse["access_token"]);
    }

    private function _uploadPost(array $arrFiles)
    {
        /**
         * Set the request parameters
         */
        $strUrl = self::CRM_API_URL . "/upload";

        $strMethod = "POST";

        $arrHeaders = [
            "Authorization" => $this->_strJWT,
            "Accept" => "application/json"
        ];

        /**
         * Format the model files into multipart form data
         */
        $arrMultipartFileData = [];
        foreach ($arrFiles as $key => $modelFile) {
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
        $guzzleResponse = self::_makeRequest(
            $strUrl,
            $strMethod,
            $arrHeaders,
            $arrMultipartFileData
        );

        $arrResponse = self::_decodeJSONResponse($guzzleResponse);

        /**
         * Set the upload path for each file from the results
         */
        foreach ($arrResponse as $key => $strGeneratedFileName) {
            $modelFile = $arrFiles[$key];

            if ($modelFile) {
                $modelFile->setUploadPath($strGeneratedFileName);

                $arrFiles[$key] = $modelFile;
            }
        }

        return $arrFiles;
    }

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

        $arrLoanData = [
            "FacilityAmountRequested" => $modelLoan->getAmount(),
            "FacilityUse"             => $modelLoan->getUse()
        ];

        /**
         * Format the entity person data
         */
        $arrEntityPersonData = [];
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
                "Title"       => $modelPerson->getTitle(),
                "Forename"    => $modelPerson->getForename(),
                "MiddleName"  => $modelPerson->getMiddleName(),
                "Surname"     => $modelPerson->getSurname(),
                "DOB"         => $modelPerson->getDateOfBirth(),
                "AddressText" => implode(
                    ' ',
                    [
                        $modelPerson->getAddressLine1(),
                        $modelPerson->getAddressLine2(),
                        $modelPerson->getAddressLine3(),
                        $modelPerson->getAddressLine4()
                    ]
                ),
                "Postcode"    => $modelPerson->getAddressPostcode(),
                "DayPhone"    => $modelPerson->getDayPhone(),
                "MobilePhone" => $modelPerson->getMobilePhone(),
                "Email"       => $modelPerson->getEmail(),
                "Notes"       => $modelPerson->getNotes(),
                "Position"    => $modelPerson->getPosition(),
                "Gender"      => $modelPerson->getGender(),
                "Files"       => $arrFiles
            ];
        }

        /**
         * Format the entity company data
         */
        $arrEntityCompanyData = [];
        foreach ($arrModelCompanies as $modelCompany) {
            $arrEntityCompanyData[] = $this->_getCompanyData($modelCompany);
        }

        $strUrl = self::CRM_API_URL;

        $strMethod = "POST";

        $arrHeaders = [
            "Authorization" => $this->_strJWT,
            "Content-Type"  => "application/json",
            "Accept"        => "application/json"
        ];

        $arrData = [
            "Primary"  => $this->_getCompanyData($modelPrimaryCompany),
            "Loan"     => $arrLoanData,
            "Entities" => array_merge(
                $arrEntityPersonData,
                $arrEntityCompanyData
            )
        ];

        /**
         * Make the case post request and decode the response
         */
        $guzzleResponse = self::_makeRequest(
            $strUrl,
            $strMethod,
            $arrHeaders,
            $arrData
        );
        $arrResponse = self::_decodeJSONResponse($guzzleResponse);

        /**
         * Return the case pk of the created case
         */
        return $arrResponse["CasePK"];
    }

    private static function _makeRequest(
        string $strUrl,
        string $strMethod,
        array $arrHeaders = [],
        $mixedData = null
    ) : ResponseInterface
    {
        $arrOptions = [];

        if ($arrHeaders) {
            $arrOptions[RequestOptions::HEADERS] = $arrHeaders;
        }

        if ($mixedData) {
            switch ($arrHeaders["Content-Type"] ?? null) {
                case "application/json":
                    $arrOptions[RequestOptions::JSON] = $mixedData;
                    break;
                default:
                    if (is_array($mixedData)) {
                        $arrOptions[RequestOptions::MULTIPART] = $mixedData;
                    } else {
                        $arrOptions[RequestOptions::FORM_PARAMS] = $mixedData;
                    }
                    break;
            }
        }

        $guzzleClient = new GuzzleClient();

        try {
            $guzzleResponse = $guzzleClient->request(
                $strMethod,
                $strUrl,
                $arrOptions
            );

        } catch (ClientException $ex) {
            $strReasonPhrase = "Empty response returned";
            $intStatusCode = 400;
            $guzzleResponse = $ex->getResponse();
            if (!is_null($guzzleResponse)) {
                $strReasonPhrase = $guzzleResponse->getReasonPhrase();
                $intStatusCode = $guzzleResponse->getStatusCode();
            }

            throw new Exception(
                sprintf(
                    "Client error returned from %s (%s)",
                    $strUrl,
                    $strReasonPhrase
                ), 
                $intStatusCode
            );

        } catch (ServerException $ex) {
            $strReasonPhrase = "Empty response returned";
            $intStatusCode = 500;
            $guzzleResponse = $ex->getResponse();
            if (!is_null($guzzleResponse)) {
                $strReasonPhrase = $guzzleResponse->getReasonPhrase();
                $intStatusCode = $guzzleResponse->getStatusCode();
            }

            throw new Exception(
                sprintf(
                    "Server error returned from %s (%s)",
                    $strUrl,
                    $strReasonPhrase
                ), 
                $intStatusCode
            );
        }

        return $guzzleResponse;
    }

    private static function _decodeJSONResponse(
        ResponseInterface $guzzleResponse
    ) : array
    {        
        $objBody = $guzzleResponse->getBody();

        /**
         * Rewind the body contents to the start and read them
         */
        $objBody->seek(0);
        $arrResponse = json_decode($objBody->getContents(), true);

        /**
         * Check the response was proper
         */
        if (!$arrResponse) {
            throw new Exception(
                "Unable to decode results from server",
                415
            );
        }

        return $arrResponse;
    }

    private function _getCompanyData(ModelCompany $modelCompany) 
    {
        $arrFileData = [];
        foreach ($modelCompany->getFiles() as $modelFile) {
            $arrFileData[] = $this->_getFileData($modelFile);
        }

        return [
            "CompanyName"               => $modelCompany->getName(),
            "LegalStatus"               => $modelCompany->getLegalStatus(),
            "TradingAddressLine1"       => $modelCompany->getTradingAddressLine1(),
            "TradingAddressLine2"       => $modelCompany->getTradingAddressLine2(),
            "TradingAddressLine3"       => $modelCompany->getTradingAddressLine3(),
            "TradingAddressLine4"       => $modelCompany->getTradingAddressLine4(),
            "TradingAddressPostcode"    => $modelCompany->getTradingAddressPostcode(),    
            "RegisteredAddressLine1"    => $modelCompany->getRegisteredAddressLine1(),    
            "RegisteredAddressLine2"    => $modelCompany->getRegisteredAddressLine2(),    
            "RegisteredAddressLine3"    => $modelCompany->getRegisteredAddressLine3(),    
            "RegisteredAddressLine4"    => $modelCompany->getRegisteredAddressLine4(),    
            "RegisteredAddressPostcode" => $modelCompany->getRegisteredAddressPostcode(),    
            "Telephone"                 => $modelCompany->getTelephone(),
            "Email"                     => $modelCompany->getEmail(),
            "Website"                   => $modelCompany->getWebsite(),
            "Notes"                     => $modelCompany->getNotes(),
            "IncorporationDate"         => $modelCompany->getIncorporationDate(),
            "CompanyRegistrationNo"     => $modelCompany->getCompanyRegistrationNumber(),
            "SicCodes"                  => $modelCompany->getSicCodes(),
            "Files"                     => $arrFileData
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