<?php

namespace jlgtechnology;

use jlgtechnology\model\{
    File as ModelFile
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
    // private const CRM_AUTH_ENDPOINT = 
    //     "https://auth.just-cashflow.com/oauth/token";
    //private const CRM_API_URL = "https://api.crm.dev.jlg-technology.com";
    private const CRM_AUTH_ENDPOINT = 
        "https://auth.just-cashflow.com/oauth/token";
    private const CRM_API_URL = "http://api.alfi.local";

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

    public function _uploadFile($mixedModelFiles)
    {
        /**
         * Get an array of the model files
         */
        $arrFiles = [];
        if ($mixedModelFiles instanceof ModelFile) {
            $arrFiles[] = $mixedModelFiles;

        } else if (is_array($mixedModelFiles)) {
            $arrFiles = $mixedModelFiles;
        }

        /**
         * Set the request parameters
         */
        $strUrl = self::CRM_API_URL . "/upload";

        $strMethod = "POST";

        $arrHeaders = [
            "Authorization" => $this->_strJWT,
            //"Content-Type" => "multipart/form-data",
            "Accept" => "application/json"
        ];

        /**
         * Format the model files into multipart form data
         */
        $arrMultipartFileData = [];
        $arrFileData = [];
        foreach ($arrFiles as $key => $modelFile) {
            $arrMultipartFileData[] = [
                "name" => strval($key),
                "contents" => fopen($modelFile->getName(), "r"),
                "headers" => [
                    "Content-Type" => $modelFile->getMimeType()
                ]
            ];

            $arrFileData[$key] = fopen($modelFile->getName(), "r");
        }

        $guzzleClient = new GuzzleClient();

        $guzzleResponse = $guzzleClient->request(
            $strMethod,
            $strUrl,
            [
                RequestOptions::HEADERS => $arrHeaders,
                RequestOptions::MULTIPART => [
                    [
                        "name" => strval($key),
                        "contents" => fopen($mixedModelFiles->getName(), "r")
                    ]
                ]
            ]
        );

        // /**
        //  * Make the request to /upload
        //  */
        // $guzzleResponse = self::_makeRequest(
        //     $strUrl,
        //     $strMethod,
        //     $arrHeaders,
        //     $arrMultipartFileData
        //     //$arrFileData
        // );

        exit(var_dump($guzzleResponse->getBody()->getContents()));

        return self::_decodeJSONResponse($guzzleResponse);
    }

    private static function _makeRequest(
        string $strUrl,
        string $strMethod,
        array $arrHeaders = [],
        array $arrData = []
    ) : ResponseInterface
    {
        $arrOptions = [];

        if ($arrHeaders) {
            $arrOptions[RequestOptions::HEADERS] = $arrHeaders;
        }

        /**
         * The content type of the data must be specified in the headers
         */
        if ($arrData) {
            switch ($arrHeaders["Content-Type"] ?? null) {
                case "application/json":
                    $arrOptions[RequestOptions::JSON] = $arrData;
                    break;
                case "multipart/form-data":
                    $arrOptions[RequestOptions::MULTIPART] = $arrData;
                    break;
                case "application/x-www-form-urlencoded":
                    $arrOptions[RequestOptions::FORM_PARAMS] = $arrData;
                    break;
                default:
                    $arrOptions[RequestOptions::BODY] = $arrData;
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
}