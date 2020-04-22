<?php

namespace jlgtechnology;

use PHPUnit\Framework\TestCase;

use jlgtechnology\model\{
    File as ModelFile,
    Company as ModelCompany,
    Person as ModelPerson,
    Loan as ModelLoan
};

use GuzzleHttp\Handler\MockHandler as GuzzleMockHandler;

use GuzzleHttp\{
    Client as GuzzleClient,
    HandlerStack as GuzzleHandlerStack,
    Middleware as GuzzleMiddleware
};

use GuzzleHttp\Psr7\{
    Response as GuzzleResponse,
    Request as GuzzleRequest
};

use Mockery;

use \DateTime as DateTime;

use \Exception as Exception;

function fopen(string $strFileName, string $strMode)
{
    return "File '$strFileName' Contents";
}

class ServiceTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testCreateFromCredentials()
    {
        $strClientId     = "Test 1";
        $strClientSecret = "Test 2";
        $strToken        = "Test 3";

        /**
         * Create the mock handler
         */
        $mockGuzzleHandler = new GuzzleMockHandler([
            new GuzzleResponse(
                200,
                ['Foo' => 'Bar'],
                json_encode(["access_token" => $strToken])
            )
        ]);

        /**
         * Create a guzzle stack from the mock handler
         */
        $objGuzzleStack = GuzzleHandlerStack::create($mockGuzzleHandler);

        /**
         * Add in a history middleware to view the requests made
         */
        $arrRequestHistory = [];
        $objGuzzleMiddleware = GuzzleMiddleware::history($arrRequestHistory);
        $objGuzzleStack->push($objGuzzleMiddleware);

        /**
         * Create a guzzle client with the stack with the mock handler and
         * request history middleware
         */
        $mockGuzzleClient = new GuzzleClient(['handler' => $objGuzzleStack]);

        /**
         * Inject guzzle client into the service
         */
        Service::setGuzzleClientForUnitTests($mockGuzzleClient);

        /**
         * Create a service from the fake credentials
         */
        $objService = Service::createFromCredentials(
            $strClientId,
            $strClientSecret
        );

        /**
         * Assert the request and result are correct
         */
        $this->assertEquals(
            $strToken,
            $objService->getToken()
        );

        $this->assertEquals(
            1,
            count($arrRequestHistory)
        );

        $this->assertEquals(
            "POST",
            $arrRequestHistory[0]["request"]->getMethod()
        );

        $this->assertEquals(
            Service::CRM_AUTH_ENDPOINT,
            $arrRequestHistory[0]["request"]->getUri()
        );

        $this->assertEquals(
            [
                "grant_type"    => "client_credentials",
                "client_id"     => $strClientId,
                "client_secret" => $strClientSecret,
                "audience"      => Service::CRM_API_URL
            ],
            json_decode(
                $arrRequestHistory[0]["request"]->getBody()->getContents(),
                true
            )
        );
    }

    public function testCreateApplication()
    {
        $strToken                     = "Test Token";

        $strPrimaryName               = "Test Primary Company";
        $strPrimaryCRN                = "64564572";
        $datePrimaryIncorporationDate = DateTime::createFromFormat(
            "Y-m-d H:i:s",
            "2001-01-01 01:01:01"
        );
        $strPrimarySicCode            = "00000";
        $strPrimaryLegalStatus        = ModelCompany::LEGAL_STATUS_SOLE_TRADER;
        $strPrimaryTradAddr1          = "Trading Address line 1";
        $strPrimaryTradAddr2          = "Trading Address line 2";
        $strPrimaryTradAddr3          = "Trading Address line 3";
        $strPrimaryTradAddr4          = "Trading Address line 4";
        $strPrimaryTradAddrPostcode   = "AB1 2CD";
        $strPrimaryRegiAddr1          = "Registered Address line 1";
        $strPrimaryRegiAddr2          = "Registered Address line 2";
        $strPrimaryRegiAddr3          = "Registered Address line 3";
        $strPrimaryRegiAddr4          = "Registered Address line 4";
        $strPrimaryRegiAddrPostcode   = "EF3 4GH";
        $strPrimaryTelephone          = "07000 000000";
        $strPrimaryEmail              = "test@email.com";
        $strPrimaryWebsite            = "www.test.com";
        $strPrimaryNotes              = "Notes about the primary company";
        $intPrimaryPosition           = 0;

        $strPrimaryFilePath        = "TestPrimaryPath";
        $strPrimaryFileName        = "TestPrimaryName";
        $strPrimaryFileMimeType    = "Test Mime Primary";
        $strPrimaryFileDescription = "A test file for the primary company";
        $intPrimaryFileCategoryId  = ModelFile::CATEGORY_SEARCHES;
        $strPrimaryFileUploadPath  = "Test Upload Path Primary";

        $intLoanFacilityAmount = 123;
        $strLoanFacilityUse    = "Test Facility Use";

        $strLoanFilePath        = "TestLoanPath";
        $strLoanFileName        = "TestLoanName";
        $strLoanFileMimeType    = "Test Mime Loan";
        $strLoanFileDescription = "A test file for the loan";
        $intLoanFileCategoryId  = ModelFile::CATEGORY_SOURCING_RESULTS;
        $strLoanFileUploadPath  = "Test Upload Path Loan";

        $strAppPersonForename       = "Test Forename";
        $strAppPersonMiddleName     = "Test Middle Name";
        $strAppPersonSurname        = "Test Surname";
        $dateAppPersonDoB           = DateTime::createFromFormat(
            "Y-m-d H:i:s",
            "2002-02-02 02:02:02"
        );
        $intAppPersonGender            = ModelPerson::GENDER_MALE;
        $strAppPersonTitle             = ModelPerson::TITLE_MR;
        $strAppPersonAddrLine1         = "Address line 1";
        $strAppPersonAddrLine2         = "Address line 2";
        $strAppPersonAddrLine3         = "Address line 3";
        $strAppPersonAddrLine4         = "Address line 4";
        $strAppPersonAddrPostcode      = "AB1 2CD";
        $strAppPersonDayPhone          = "07000 000000";
        $strAppPersonMobilePhone       = "07000 000001";
        $strAppPersonEmail             = "test@email.com";
        $strAppPersonNotes             = "Notes about the applicant person";
        $intAppPersonPosition          = ModelCompany::POSITION_DIRECTOR_BIT;
        $boolAppPersonIsPrimaryContact = true;

        $strAppPersonFilePath        = "TestApplicantPersonPath";
        $strAppPersonFileName        = "TestApplicantPersonName";
        $strAppPersonFileMimeType    = "Test MimeType Applicant Person";
        $strAppPersonFileDescription = "A test file for the applicant person";
        $intAppPersonFileCategoryId  = ModelFile::CATEGORY_GUARANTOR_DETAILS;
        $strAppPersonFileUploadPath  = "Test Upload Path Applicant Person";
        
        $strAppPersonPassportForename = 'PassForename';
        $strAppPersonPassportMiddleName = 'PassMiddleName';
        $strAppPersonPassportSurname = 'PassSurname';

        $strAppCompanyName               = "Test Applicant Company";
        $strAppCompanyCRN                = "64564572";
        $dateAppCompanyIncorporationDate = DateTime::createFromFormat(
            "Y-m-d H:i:s",
            "2003-03-03 03:03:03"
        );
        $strAppCompanySicCode            = "00001";
        $strAppCompanyLegalStatus        = ModelCompany::LEGAL_STATUS_CHARITY;
        $strAppCompanyTradAddr1          = "Trading Address line 1";
        $strAppCompanyTradAddr2          = "Trading Address line 2";
        $strAppCompanyTradAddr3          = "Trading Address line 3";
        $strAppCompanyTradAddr4          = "Trading Address line 4";
        $strAppCompanyTradAddrPostcode   = "AB1 2CD";
        $strAppCompanyRegiAddr1          = "Registered Address line 1";
        $strAppCompanyRegiAddr2          = "Registered Address line 2";
        $strAppCompanyRegiAddr3          = "Registered Address line 3";
        $strAppCompanyRegiAddr4          = "Registered Address line 4";
        $strAppCompanyRegiAddrPostcode   = "EF3 4GH";
        $strAppCompanyTelephone          = "07000 000000";
        $strAppCompanyEmail              = "test@email.com";
        $strAppCompanyWebsite            = "www.test.com";
        $strAppCompanyNotes              = "Notes about the applicant company";
        $intAppCompanyPosition           = ModelCompany::POSITION_PSC_BIT;

        $strAppCompanyFilePath        = "TestApplicantCompanyPath";
        $strAppCompanyFileName        = "TestApplicantCompanyName";
        $strAppCompanyFileMimeType    = "Test MimeType Applicant Company";
        $strAppCompanyFileDescription = "A test file for the applicant company";
        $intAppCompanyFileCategoryId  = ModelFile::CATEGORY_C19_SURVEY;
        $strAppCompanyFileUploadPath  = "Test Upload Path Applicant Company";

        $intCasePK = 999;

        /**
         * Create mock models that'll be passed to Service::createApplication()
         */
         /**
          * Primary company models
          */
        $mockPrimaryFile = Mockery::mock(ModelFile::class)
            ->shouldReceive("getNameAndPath")
            ->withNoArgs()
            ->andReturn($strPrimaryFilePath . '/' . $strPrimaryFileName)
            ->twice()
            ->shouldReceive("getMimeType")
            ->withNoArgs()
            ->andReturn($strPrimaryFileMimeType)
            ->twice()
            ->shouldReceive("getDescription")
            ->withNoArgs()
            ->andReturn($strPrimaryFileDescription)
            ->once()
            ->shouldReceive("getCategoryId")
            ->withNoArgs()
            ->andReturn($intPrimaryFileCategoryId)
            ->once()
            ->shouldReceive("getUploadPath")
            ->withNoArgs()
            ->andReturn($strPrimaryFileUploadPath)
            ->once()
            ->shouldReceive("setUploadPath")
            ->with($strPrimaryFileUploadPath)
            ->andReturnSelf()
            ->once()
            ->mock();

        $mockModelPrimaryCompany = Mockery::mock(ModelCompany::class)
            ->shouldReceive("getFiles")
            ->withNoArgs()
            ->andReturn([$mockPrimaryFile])
            ->twice()
            ->shouldReceive("getName")
            ->withNoArgs()
            ->andReturn($strPrimaryName)
            ->once()
            ->shouldReceive("getLegalStatus")
            ->withNoArgs()
            ->andReturn($strPrimaryLegalStatus)
            ->once()
            ->shouldReceive("getTradingAddressLine1")
            ->withNoArgs()
            ->andReturn($strPrimaryTradAddr1)
            ->once()
            ->shouldReceive("getTradingAddressLine2")
            ->withNoArgs()
            ->andReturn($strPrimaryTradAddr2)
            ->once()
            ->shouldReceive("getTradingAddressLine3")
            ->withNoArgs()
            ->andReturn($strPrimaryTradAddr3)
            ->once()
            ->shouldReceive("getTradingAddressLine4")
            ->withNoArgs()
            ->andReturn($strPrimaryTradAddr4)
            ->once()
            ->shouldReceive("getTradingAddressPostcode")
            ->withNoArgs()
            ->andReturn($strPrimaryTradAddrPostcode)
            ->once()
            ->shouldReceive("getRegisteredAddressLine1")
            ->withNoArgs()
            ->andReturn($strPrimaryRegiAddr1)
            ->once()
            ->shouldReceive("getRegisteredAddressLine2")
            ->withNoArgs()
            ->andReturn($strPrimaryRegiAddr2)
            ->once()
            ->shouldReceive("getRegisteredAddressLine3")
            ->withNoArgs()
            ->andReturn($strPrimaryRegiAddr3)
            ->once()
            ->shouldReceive("getRegisteredAddressLine4")
            ->withNoArgs()
            ->andReturn($strPrimaryRegiAddr4)
            ->once()
            ->shouldReceive("getRegisteredAddressPostcode")
            ->withNoArgs()
            ->andReturn($strPrimaryRegiAddrPostcode)
            ->once()
            ->shouldReceive("getTelephone")
            ->withNoArgs()
            ->andReturn($strPrimaryTelephone)
            ->once()
            ->shouldReceive("getEmail")
            ->withNoArgs()
            ->andReturn($strPrimaryEmail)
            ->once()
            ->shouldReceive("getWebsite")
            ->withNoArgs()
            ->andReturn($strPrimaryWebsite)
            ->once()
            ->shouldReceive("getNotes")
            ->withNoArgs()
            ->andReturn($strPrimaryNotes)
            ->once()
            ->shouldReceive("getIncorporationDate")
            ->withNoArgs()
            ->andReturn($datePrimaryIncorporationDate)
            ->once()
            ->shouldReceive("getCompanyRegistrationNumber")
            ->withNoArgs()
            ->andReturn($strPrimaryCRN)
            ->once()
            ->shouldReceive("getSicCodes")
            ->withNoArgs()
            ->andReturn($strPrimarySicCode)
            ->once()
            ->shouldReceive("getPosition")
            ->withNoArgs()
            ->andReturn($intPrimaryPosition)
            ->once()
            ->mock();

        /**
         * Loan models
         */
        $mockLoanFile = Mockery::mock(ModelFile::class)
            ->shouldReceive("getNameAndPath")
            ->withNoArgs()
            ->andReturn($strLoanFilePath . '/' . $strLoanFileName)
            ->twice()
            ->shouldReceive("getMimeType")
            ->withNoArgs()
            ->andReturn($strLoanFileMimeType)
            ->twice()
            ->shouldReceive("getDescription")
            ->withNoArgs()
            ->andReturn($strLoanFileDescription)
            ->once()
            ->shouldReceive("getCategoryId")
            ->withNoArgs()
            ->andReturn($intLoanFileCategoryId)
            ->once()
            ->shouldReceive("getUploadPath")
            ->withNoArgs()
            ->andReturn($strLoanFileUploadPath)
            ->once()
            ->shouldReceive("setUploadPath")
            ->with($strLoanFileUploadPath)
            ->andReturnSelf()
            ->once()
            ->mock();

        $mockModelLoan = Mockery::mock(ModelLoan::class)
            ->shouldReceive("getFiles")
            ->withNoArgs()
            ->andReturn([$mockLoanFile])
            ->twice()
            ->shouldReceive("getAmount")
            ->withNoArgs()
            ->andReturn($intLoanFacilityAmount)
            ->once()
            ->shouldReceive("getUse")
            ->withNoArgs()
            ->andReturn($strLoanFacilityUse)
            ->once()
            ->mock();

        /**
         * Applicant person models
         */
        $mockAppPersonFile = Mockery::mock(ModelFile::class)
            ->shouldReceive("getNameAndPath")
            ->withNoArgs()
            ->andReturn($strAppPersonFilePath . '/' . $strAppPersonFileName)
            ->twice()
            ->shouldReceive("getMimeType")
            ->withNoArgs()
            ->andReturn($strAppPersonFileMimeType)
            ->twice()
            ->shouldReceive("getDescription")
            ->withNoArgs()
            ->andReturn($strAppPersonFileDescription)
            ->once()
            ->shouldReceive("getCategoryId")
            ->withNoArgs()
            ->andReturn($intAppPersonFileCategoryId)
            ->once()
            ->shouldReceive("getUploadPath")
            ->withNoArgs()
            ->andReturn($strAppPersonFileUploadPath)
            ->once()
            ->shouldReceive("setUploadPath")
            ->with($strAppPersonFileUploadPath)
            ->andReturnSelf()
            ->once()
            ->mock();

        $mockModelAppPerson = Mockery::mock(ModelPerson::class)
            ->shouldReceive("getFiles")
            ->withNoArgs()
            ->andReturn([$mockAppPersonFile])
            ->twice()
            ->shouldReceive("getTitle")
            ->withNoArgs()
            ->andReturn($strAppPersonTitle)
            ->once()
            ->shouldReceive("getForename")
            ->withNoArgs()
            ->andReturn($strAppPersonForename)
            ->twice()
            ->shouldReceive("getMiddleName")
            ->withNoArgs()
            ->andReturn($strAppPersonMiddleName)
            ->once()
            ->shouldReceive("getSurname")
            ->withNoArgs()
            ->andReturn($strAppPersonSurname)
            ->twice()
            ->shouldReceive("getDateOfBirth")
            ->withNoArgs()
            ->andReturn($dateAppPersonDoB)
            ->once()
            ->shouldReceive("getAddressLine1")
            ->withNoArgs()
            ->andReturn($strAppPersonAddrLine1)
            ->once()
            ->shouldReceive("getAddressLine2")
            ->withNoArgs()
            ->andReturn($strAppPersonAddrLine2)
            ->once()
            ->shouldReceive("getAddressLine3")
            ->withNoArgs()
            ->andReturn($strAppPersonAddrLine3)
            ->once()
            ->shouldReceive("getAddressLine4")
            ->withNoArgs()
            ->andReturn($strAppPersonAddrLine4)
            ->once()
            ->shouldReceive("getAddressPostcode")
            ->withNoArgs()
            ->andReturn($strAppPersonAddrPostcode)
            ->once()
            ->shouldReceive("getDayPhone")
            ->withNoArgs()
            ->andReturn($strAppPersonDayPhone)
            ->once()
            ->shouldReceive("getMobilePhone")
            ->withNoArgs()
            ->andReturn($strAppPersonMobilePhone)
            ->once()
            ->shouldReceive("getEmail")
            ->withNoArgs()
            ->andReturn($strAppPersonEmail)
            ->once()
            ->shouldReceive("getNotes")
            ->withNoArgs()
            ->andReturn($strAppPersonNotes)
            ->once()
            ->shouldReceive("getPosition")
            ->withNoArgs()
            ->andReturn($intAppPersonPosition)
            ->once()
            ->shouldReceive("getGender")
            ->withNoArgs()
            ->andReturn($intAppPersonGender)
            ->once()
            ->shouldReceive("getIsPrimaryContact")
            ->withNoArgs()
            ->andReturn($boolAppPersonIsPrimaryContact)
            ->once()
            ->shouldReceive("getPassportForename")
            ->withNoArgs()
            ->andReturn($strAppPersonPassportForename)
            ->once()
            ->shouldReceive("getPassportMiddleName")
            ->withNoArgs()
            ->andReturn($strAppPersonPassportMiddleName)
            ->once()
            ->shouldReceive("getPassportSurname")
            ->withNoArgs()
            ->andReturn($strAppPersonPassportSurname)
            ->once()
            ->mock();



        /**
         * Applicant company models
         */
        $mockAppCompanyFile = Mockery::mock(ModelFile::class)
            ->shouldReceive("getNameAndPath")
            ->withNoArgs()
            ->andReturn($strAppCompanyFilePath . '/' . $strAppCompanyFileName)
            ->twice()
            ->shouldReceive("getMimeType")
            ->withNoArgs()
            ->andReturn($strAppCompanyFileMimeType)
            ->twice()
            ->shouldReceive("getDescription")
            ->withNoArgs()
            ->andReturn($strAppCompanyFileDescription)
            ->once()
            ->shouldReceive("getCategoryId")
            ->withNoArgs()
            ->andReturn($intAppCompanyFileCategoryId)
            ->once()
            ->shouldReceive("getUploadPath")
            ->withNoArgs()
            ->andReturn($strAppCompanyFileUploadPath)
            ->once()
            ->shouldReceive("setUploadPath")
            ->with($strAppCompanyFileUploadPath)
            ->andReturnSelf()
            ->once()
            ->mock();

        $mockModelAppCompany = Mockery::mock(ModelCompany::class)
            ->shouldReceive("getFiles")
            ->withNoArgs()
            ->andReturn([$mockAppCompanyFile])
            ->twice()
            ->shouldReceive("getName")
            ->withNoArgs()
            ->andReturn($strAppCompanyName)
            ->once()
            ->shouldReceive("getLegalStatus")
            ->withNoArgs()
            ->andReturn($strAppCompanyLegalStatus)
            ->once()
            ->shouldReceive("getTradingAddressLine1")
            ->withNoArgs()
            ->andReturn($strAppCompanyTradAddr1)
            ->once()
            ->shouldReceive("getTradingAddressLine2")
            ->withNoArgs()
            ->andReturn($strAppCompanyTradAddr2)
            ->once()
            ->shouldReceive("getTradingAddressLine3")
            ->withNoArgs()
            ->andReturn($strAppCompanyTradAddr3)
            ->once()
            ->shouldReceive("getTradingAddressLine4")
            ->withNoArgs()
            ->andReturn($strAppCompanyTradAddr4)
            ->once()
            ->shouldReceive("getTradingAddressPostcode")
            ->withNoArgs()
            ->andReturn($strAppCompanyTradAddrPostcode)
            ->once()
            ->shouldReceive("getRegisteredAddressLine1")
            ->withNoArgs()
            ->andReturn($strAppCompanyRegiAddr1)
            ->once()
            ->shouldReceive("getRegisteredAddressLine2")
            ->withNoArgs()
            ->andReturn($strAppCompanyRegiAddr2)
            ->once()
            ->shouldReceive("getRegisteredAddressLine3")
            ->withNoArgs()
            ->andReturn($strAppCompanyRegiAddr3)
            ->once()
            ->shouldReceive("getRegisteredAddressLine4")
            ->withNoArgs()
            ->andReturn($strAppCompanyRegiAddr4)
            ->once()
            ->shouldReceive("getRegisteredAddressPostcode")
            ->withNoArgs()
            ->andReturn($strAppCompanyRegiAddrPostcode)
            ->once()
            ->shouldReceive("getTelephone")
            ->withNoArgs()
            ->andReturn($strAppCompanyTelephone)
            ->once()
            ->shouldReceive("getEmail")
            ->withNoArgs()
            ->andReturn($strAppCompanyEmail)
            ->once()
            ->shouldReceive("getWebsite")
            ->withNoArgs()
            ->andReturn($strAppCompanyWebsite)
            ->once()
            ->shouldReceive("getNotes")
            ->withNoArgs()
            ->andReturn($strAppCompanyNotes)
            ->once()
            ->shouldReceive("getIncorporationDate")
            ->withNoArgs()
            ->andReturn($dateAppCompanyIncorporationDate)
            ->once()
            ->shouldReceive("getCompanyRegistrationNumber")
            ->withNoArgs()
            ->andReturn($strAppCompanyCRN)
            ->once()
            ->shouldReceive("getSicCodes")
            ->withNoArgs()
            ->andReturn($strAppCompanySicCode)
            ->once()
            ->shouldReceive("getPosition")
            ->withNoArgs()
            ->andReturn($intAppCompanyPosition)
            ->once()
            ->mock();

        /**
         * Create the mock handler
         */
        $mockGuzzleHandler = new GuzzleMockHandler([
            new GuzzleResponse(
                200,
                ['Foo' => 'Bar'],
                json_encode([
                    $strPrimaryFileUploadPath,   // Note this should be in the
                    $strLoanFileUploadPath,      // same order as the array of
                    $strAppPersonFileUploadPath, // files passed to _uploadPost
                    $strAppCompanyFileUploadPath
                ])
            ),
            new GuzzleResponse(
                200,
                ['Foo' => 'Bar'],
                json_encode(["CasePK" => $intCasePK])
            )
        ]);

        /**
         * Create a guzzle stack from the mock handler
         */
        $objGuzzleStack = GuzzleHandlerStack::create($mockGuzzleHandler);

        /**
         * Add in a history middleware to view the requests made
         */
        $arrRequestHistory = [];
        $objGuzzleMiddleware = GuzzleMiddleware::history($arrRequestHistory);
        $objGuzzleStack->push($objGuzzleMiddleware);

        /**
         * Create a guzzle client with the stack with the mock handler and
         * request history middleware
         */
        $mockGuzzleClient = new GuzzleClient(['handler' => $objGuzzleStack]);

        /**
         * Inject guzzle client into the service
         */
        Service::setGuzzleClientForUnitTests($mockGuzzleClient);

        $objService = Service::createFromToken($strToken);

        /**
         * Create an application with the mock models
         */
        $response = $objService->createApplication(
            $mockModelPrimaryCompany,
            $mockModelLoan,
            [$mockModelAppPerson, $mockModelAppCompany]
        );

        $this->assertEquals(
            $intCasePK,
            $response
        );

        $this->assertEquals(
            2,
            count($arrRequestHistory)
        );

        /**
         * Validate the upload POST request was properly formatted
         * Since it's sent as multipart/form-data it's not possible to validate
         * the body is as expected
         */
        $guzzleRequestUpload = $arrRequestHistory[0]["request"];

        $this->assertEquals(
            "POST",
            $guzzleRequestUpload->getMethod()
        );

        $this->assertEquals(
            Service::CRM_API_URL . "/upload",
            (string)$guzzleRequestUpload->getUri()
        );

        /**
         * Validate the case POST request was properly formatted
         */
        $guzzleRequestCase = $arrRequestHistory[1]["request"];

        $this->assertEquals(
            "POST",
            $guzzleRequestCase->getMethod()
        );

        $this->assertEquals(
            Service::CRM_API_URL . "/case",
            (string)$guzzleRequestCase->getUri()
        );

        $this->assertEquals(
            [
                "Primary" => [
                    "CompanyName"               => $strPrimaryName,
                    "LegalStatus"               => $strPrimaryLegalStatus,
                    "TradingAddressLine1"       => $strPrimaryTradAddr1,
                    "TradingAddressLine2"       => $strPrimaryTradAddr2,
                    "TradingAddressLine3"       => $strPrimaryTradAddr3,
                    "TradingAddressLine4"       => $strPrimaryTradAddr4,
                    "TradingAddressPostcode"    => $strPrimaryTradAddrPostcode,
                    "RegisteredAddressLine1"    => $strPrimaryRegiAddr1,
                    "RegisteredAddressLine2"    => $strPrimaryRegiAddr2,
                    "RegisteredAddressLine3"    => $strPrimaryRegiAddr3,
                    "RegisteredAddressLine4"    => $strPrimaryRegiAddr4,
                    "RegisteredAddressPostcode" => $strPrimaryRegiAddrPostcode,
                    "Telephone"                 => $strPrimaryTelephone,
                    "Email"                     => $strPrimaryEmail,
                    "Website"                   => $strPrimaryWebsite,
                    "Notes"                     => $strPrimaryNotes,
                    "IncorporationDate"         => $datePrimaryIncorporationDate
                        ->format("Y-m-d H:i:s"),
                    "CompanyRegistrationNo"     => $strPrimaryCRN,
                    "SicCodes"                  => $strPrimarySicCode,
                    "Position"                  => $intPrimaryPosition,
                    "Files"                     => [
                        [
                            "FileName"          => $strPrimaryFileName,
                            "GeneratedFileName" => $strPrimaryFileUploadPath,
                            "Description"       => $strPrimaryFileDescription,
                            "CategoryID"        => $intPrimaryFileCategoryId,
                            "MimeType"          => $strPrimaryFileMimeType
                        ]
                    ]
                ],
                "Loan" => [
                    "FacilityAmountRequested" => $intLoanFacilityAmount,
                    "FacilityUse"             => $strLoanFacilityUse,
                    "Files"                   => [
                        [
                            "FileName"          => $strLoanFileName,
                            "GeneratedFileName" => $strLoanFileUploadPath,
                            "Description"       => $strLoanFileDescription,
                            "CategoryID"        => $intLoanFileCategoryId,
                            "MimeType"          => $strLoanFileMimeType
                        ]
                    ]
                ],
                "Entities" => [
                    [
                        "Type"            => "Person",
                        "Title"           => $strAppPersonTitle,
                        "Forename"        => $strAppPersonForename,
                        "MiddleName"      => $strAppPersonMiddleName,
                        "Surname"         => $strAppPersonSurname,
                        "DOB"             => $dateAppPersonDoB
                            ->format("Y-m-d H:i:s"),
                        "AddressLine1"    => $strAppPersonAddrLine1,
                        "AddressLine2"    => $strAppPersonAddrLine2,
                        "AddressLine3"    => $strAppPersonAddrLine3,
                        "AddressLine4"    => $strAppPersonAddrLine4,
                        "AddressPostcode" => $strAppPersonAddrPostcode,
                        "DayPhone"        => $strAppPersonDayPhone,
                        "MobilePhone"     => $strAppPersonMobilePhone,
                        "Email"           => $strAppPersonEmail,
                        "Notes"           => $strAppPersonNotes,
                        "Position"        => $intAppPersonPosition,
                        "Gender"          => $intAppPersonGender,
                        "Files"           => [
                            [
                                "FileName"          => 
                                    $strAppPersonFileName,
                                "GeneratedFileName" => 
                                    $strAppPersonFileUploadPath,
                                "Description"       => 
                                    $strAppPersonFileDescription,
                                "CategoryID"        => 
                                    $intAppPersonFileCategoryId,
                                "MimeType"          => 
                                    $strAppPersonFileMimeType
                            ]
                        ],
                        "PassportForename" => $strAppPersonPassportForename,
                        "PassportMiddleName" => $strAppPersonPassportMiddleName,
                        "PassportSurname" => $strAppPersonPassportSurname
                    ],
                    [
                        "Type"                      => "Company",
                        "CompanyName"               => $strAppCompanyName,
                        "LegalStatus"               => 
                            $strAppCompanyLegalStatus,
                        "TradingAddressLine1"       => $strAppCompanyTradAddr1,
                        "TradingAddressLine2"       => $strAppCompanyTradAddr2,
                        "TradingAddressLine3"       => $strAppCompanyTradAddr3,
                        "TradingAddressLine4"       => $strAppCompanyTradAddr4,
                        "TradingAddressPostcode"    => 
                            $strAppCompanyTradAddrPostcode,
                        "RegisteredAddressLine1"    => $strAppCompanyRegiAddr1,
                        "RegisteredAddressLine2"    => $strAppCompanyRegiAddr2,
                        "RegisteredAddressLine3"    => $strAppCompanyRegiAddr3,
                        "RegisteredAddressLine4"    => $strAppCompanyRegiAddr4,
                        "RegisteredAddressPostcode" => 
                            $strAppCompanyRegiAddrPostcode,
                        "Telephone"                 => $strAppCompanyTelephone,
                        "Email"                     => $strAppCompanyEmail,
                        "Website"                   => $strAppCompanyWebsite,
                        "Notes"                     => $strAppCompanyNotes,
                        "IncorporationDate"         => 
                            $dateAppCompanyIncorporationDate
                                ->format("Y-m-d H:i:s"),
                        "CompanyRegistrationNo"     => $strAppCompanyCRN,
                        "SicCodes"                  => $strAppCompanySicCode,
                        "Position"                  => $intAppCompanyPosition,
                        "Files"                     => [
                            [
                                "FileName"          => 
                                    $strAppCompanyFileName,
                                "GeneratedFileName" => 
                                    $strAppCompanyFileUploadPath,
                                "Description"       => 
                                    $strAppCompanyFileDescription,
                                "CategoryID"        => 
                                    $intAppCompanyFileCategoryId,
                                "MimeType"          => 
                                    $strAppCompanyFileMimeType
                            ]
                        ]
                    ]
                ],
                "PrimaryContactName" => $strAppPersonForename . " " . 
                    $strAppPersonSurname
            ],
            json_decode(
                $guzzleRequestCase->getBody()->getContents(),
                true
            )
        );
    }

    public function testCreateApplication_Invalid_Applicant_Class()
    {
        $mockModelPrimaryCompany = Mockery::mock(ModelCompany::class);

        $mockModelLoan = Mockery::mock(ModelLoan::class);

        $mockModelFile = Mockery::mock(ModelFile::class);

        $objService = Service::createFromToken("Test Token");

        /**
         * We expect an exception because we provided a ModelFile as an 
         * applicant instead of a ModelPerson or ModelCompany
         */
        $this->expectException(Exception::class);

        $response = $objService->createApplication(
            $mockModelPrimaryCompany,
            $mockModelLoan,
            [$mockModelFile]
        );
    }

    public function testCreateApplication_No_Primary_Contact()
    {
        $strToken                     = "Test Token";

        $strPrimaryName               = "Test Primary Company";
        $strPrimaryCRN                = "64564572";
        $datePrimaryIncorporationDate = DateTime::createFromFormat(
            "Y-m-d H:i:s",
            "2001-01-01 01:01:01"
        );
        $strPrimarySicCode            = "00000";
        $strPrimaryLegalStatus        = ModelCompany::LEGAL_STATUS_SOLE_TRADER;
        $strPrimaryTradAddr1          = "Trading Address line 1";
        $strPrimaryTradAddr2          = "Trading Address line 2";
        $strPrimaryTradAddr3          = "Trading Address line 3";
        $strPrimaryTradAddr4          = "Trading Address line 4";
        $strPrimaryTradAddrPostcode   = "AB1 2CD";
        $strPrimaryRegiAddr1          = "Registered Address line 1";
        $strPrimaryRegiAddr2          = "Registered Address line 2";
        $strPrimaryRegiAddr3          = "Registered Address line 3";
        $strPrimaryRegiAddr4          = "Registered Address line 4";
        $strPrimaryRegiAddrPostcode   = "EF3 4GH";
        $strPrimaryTelephone          = "07000 000000";
        $strPrimaryEmail              = "test@email.com";
        $strPrimaryWebsite            = "www.test.com";
        $strPrimaryNotes              = "Notes about the primary company";
        $intPrimaryPosition           = 0;

        $intLoanFacilityAmount = 123;
        $strLoanFacilityUse    = "Test Facility Use";

        $strAppPersonForename       = "Test Forename";
        $strAppPersonMiddleName     = "Test Middle Name";
        $strAppPersonSurname        = "Test Surname";
        $dateAppPersonDoB           = DateTime::createFromFormat(
            "Y-m-d H:i:s",
            "2002-02-02 02:02:02"
        );
        $intAppPersonGender            = ModelPerson::GENDER_MALE;
        $strAppPersonTitle             = ModelPerson::TITLE_MR;
        $strAppPersonAddrLine1         = "Address line 1";
        $strAppPersonAddrLine2         = "Address line 2";
        $strAppPersonAddrLine3         = "Address line 3";
        $strAppPersonAddrLine4         = "Address line 4";
        $strAppPersonAddrPostcode      = "AB1 2CD";
        $strAppPersonDayPhone          = "07000 000000";
        $strAppPersonMobilePhone       = "07000 000001";
        $strAppPersonEmail             = "test@email.com";
        $strAppPersonNotes             = "Notes about the applicant person";
        $intAppPersonPosition          = ModelCompany::POSITION_DIRECTOR_BIT;
        $boolAppPersonIsPrimaryContact = false;
        $strAppPersonPassportForename = 'PassForename';
        $strAppPersonPassportMiddleName = 'PassMiddleName';
        $strAppPersonPassportSurname = 'PassSurname';

        $intCasePK = 999;

        /**
         * Create mock models that'll be passed to Service::createApplication()
         */
         /**
          * Primary company model
          */
        $mockModelPrimaryCompany = Mockery::mock(ModelCompany::class)
            ->shouldReceive("getFiles")
            ->withNoArgs()
            ->andReturn([])
            ->twice()
            ->shouldReceive("getName")
            ->withNoArgs()
            ->andReturn($strPrimaryName)
            ->once()
            ->shouldReceive("getLegalStatus")
            ->withNoArgs()
            ->andReturn($strPrimaryLegalStatus)
            ->once()
            ->shouldReceive("getTradingAddressLine1")
            ->withNoArgs()
            ->andReturn($strPrimaryTradAddr1)
            ->once()
            ->shouldReceive("getTradingAddressLine2")
            ->withNoArgs()
            ->andReturn($strPrimaryTradAddr2)
            ->once()
            ->shouldReceive("getTradingAddressLine3")
            ->withNoArgs()
            ->andReturn($strPrimaryTradAddr3)
            ->once()
            ->shouldReceive("getTradingAddressLine4")
            ->withNoArgs()
            ->andReturn($strPrimaryTradAddr4)
            ->once()
            ->shouldReceive("getTradingAddressPostcode")
            ->withNoArgs()
            ->andReturn($strPrimaryTradAddrPostcode)
            ->once()
            ->shouldReceive("getRegisteredAddressLine1")
            ->withNoArgs()
            ->andReturn($strPrimaryRegiAddr1)
            ->once()
            ->shouldReceive("getRegisteredAddressLine2")
            ->withNoArgs()
            ->andReturn($strPrimaryRegiAddr2)
            ->once()
            ->shouldReceive("getRegisteredAddressLine3")
            ->withNoArgs()
            ->andReturn($strPrimaryRegiAddr3)
            ->once()
            ->shouldReceive("getRegisteredAddressLine4")
            ->withNoArgs()
            ->andReturn($strPrimaryRegiAddr4)
            ->once()
            ->shouldReceive("getRegisteredAddressPostcode")
            ->withNoArgs()
            ->andReturn($strPrimaryRegiAddrPostcode)
            ->once()
            ->shouldReceive("getTelephone")
            ->withNoArgs()
            ->andReturn($strPrimaryTelephone)
            ->once()
            ->shouldReceive("getEmail")
            ->withNoArgs()
            ->andReturn($strPrimaryEmail)
            ->once()
            ->shouldReceive("getWebsite")
            ->withNoArgs()
            ->andReturn($strPrimaryWebsite)
            ->once()
            ->shouldReceive("getNotes")
            ->withNoArgs()
            ->andReturn($strPrimaryNotes)
            ->once()
            ->shouldReceive("getIncorporationDate")
            ->withNoArgs()
            ->andReturn($datePrimaryIncorporationDate)
            ->once()
            ->shouldReceive("getCompanyRegistrationNumber")
            ->withNoArgs()
            ->andReturn($strPrimaryCRN)
            ->once()
            ->shouldReceive("getSicCodes")
            ->withNoArgs()
            ->andReturn($strPrimarySicCode)
            ->once()
            ->shouldReceive("getPosition")
            ->withNoArgs()
            ->andReturn($intPrimaryPosition)
            ->once()
            ->mock();

        /**
         * Loan model
         */
        $mockModelLoan = Mockery::mock(ModelLoan::class)
            ->shouldReceive("getFiles")
            ->withNoArgs()
            ->andReturn([])
            ->twice()
            ->shouldReceive("getAmount")
            ->withNoArgs()
            ->andReturn($intLoanFacilityAmount)
            ->once()
            ->shouldReceive("getUse")
            ->withNoArgs()
            ->andReturn($strLoanFacilityUse)
            ->once()
            ->mock();

        /**
         * Applicant person model
         */
        $mockModelAppPerson = Mockery::mock(ModelPerson::class)
            ->shouldReceive("getFiles")
            ->withNoArgs()
            ->andReturn([])
            ->twice()
            ->shouldReceive("getTitle")
            ->withNoArgs()
            ->andReturn($strAppPersonTitle)
            ->once()
            ->shouldReceive("getForename")
            ->withNoArgs()
            ->andReturn($strAppPersonForename)
            ->once()
            ->shouldReceive("getMiddleName")
            ->withNoArgs()
            ->andReturn($strAppPersonMiddleName)
            ->once()
            ->shouldReceive("getSurname")
            ->withNoArgs()
            ->andReturn($strAppPersonSurname)
            ->once()
            ->shouldReceive("getDateOfBirth")
            ->withNoArgs()
            ->andReturn($dateAppPersonDoB)
            ->once()
            ->shouldReceive("getAddressLine1")
            ->withNoArgs()
            ->andReturn($strAppPersonAddrLine1)
            ->once()
            ->shouldReceive("getAddressLine2")
            ->withNoArgs()
            ->andReturn($strAppPersonAddrLine2)
            ->once()
            ->shouldReceive("getAddressLine3")
            ->withNoArgs()
            ->andReturn($strAppPersonAddrLine3)
            ->once()
            ->shouldReceive("getAddressLine4")
            ->withNoArgs()
            ->andReturn($strAppPersonAddrLine4)
            ->once()
            ->shouldReceive("getAddressPostcode")
            ->withNoArgs()
            ->andReturn($strAppPersonAddrPostcode)
            ->once()
            ->shouldReceive("getDayPhone")
            ->withNoArgs()
            ->andReturn($strAppPersonDayPhone)
            ->once()
            ->shouldReceive("getMobilePhone")
            ->withNoArgs()
            ->andReturn($strAppPersonMobilePhone)
            ->once()
            ->shouldReceive("getEmail")
            ->withNoArgs()
            ->andReturn($strAppPersonEmail)
            ->once()
            ->shouldReceive("getNotes")
            ->withNoArgs()
            ->andReturn($strAppPersonNotes)
            ->once()
            ->shouldReceive("getPosition")
            ->withNoArgs()
            ->andReturn($intAppPersonPosition)
            ->once()
            ->shouldReceive("getGender")
            ->withNoArgs()
            ->andReturn($intAppPersonGender)
            ->once()
            ->shouldReceive("getIsPrimaryContact")
            ->withNoArgs()
            ->andReturn($boolAppPersonIsPrimaryContact)
            ->once()
            ->shouldReceive("getPassportForename")
            ->withNoArgs()
            ->andReturn($strAppPersonPassportForename)
            ->once()
            ->shouldReceive("getPassportMiddleName")
            ->withNoArgs()
            ->andReturn($strAppPersonPassportMiddleName)
            ->once()
            ->shouldReceive("getPassportSurname")
            ->withNoArgs()
            ->andReturn($strAppPersonPassportSurname)
            ->once()
            ->mock();

        $objService = Service::createFromToken("Test Token");

        /**
         * We expect an exception because there is no primary contact
         */
        $this->expectException(Exception::class);

        $objService->createApplication(
            $mockModelPrimaryCompany,
            $mockModelLoan,
            [$mockModelAppPerson]
        );
    }
}