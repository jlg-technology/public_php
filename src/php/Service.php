<?php

namespace jlgtechnology;

use GuzzleHttp\{
    Client as GuzzleClient,
    RequestOptions as GuzzleRequestOptions
};

use GuzzleHttp\Psr7\Response as GuzzleResponse;

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

    private static function _makeRequest(
        string $strUrl,
        string $strMethod,
        array $arrHeaders = [],
        array $arrData = []
    ) : GuzzleResponse
    {
        $arrOptions = [];

        if ($arrHeaders) {
            $arrOptions[GuzzleRequestOptions::HEADERS] = $arrHeaders;
        }

        /**
         * If the content-type is json then we need to set it specially
         */
        if (
            $arrData &&
            $arrHeaders && 
            (
                in_array("Content-Type: application/json", $arrHeaders) ||
                (
                    array_key_exists("Content-Type", $arrHeaders) && 
                    $arrHeaders["Content-Type"] === "application/json"
                )
            )
        ) {
            $arrOptions[GuzzleRequestOptions::JSON] = $arrData;
        } else {
            $arrOptions[GuzzleRequestOptions::FORM_PARAMS] = $arrData;
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
            if (!is_null($guzzleResponse = $ex->getResponse())) {
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
            if (!is_null($guzzleResponse = $ex->getResponse())) {
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
        GuzzleResponse $guzzleResponse
    ) : array
    {        
        $objBody = $guzzleResponse->getBody();

        /**
         * Rewind the body contents to the start
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