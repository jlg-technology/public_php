<?php

namespace jlgtechnology;

use PHPUnit\Framework\TestCase;

use jlgtechnology\model\{
    File as ModelFile,
    Company as ModelCompany,
    Person as ModelPerson,
    Loan as ModelLoan
};

use \DateTime as DateTime;

use \Exception as Exception;

class ServiceTest extends TestCase
{
    private $_strClientId     = "DlAaul2JB6vKVrlOQ0VB0NNjoIG443H4";
    private $_strClientSecret = 
        "wYlA3pjtEYM6jOxdtGn4F2WqvIWI3k6fWIxFOeZpWWas2flxYpxOx14LVBCSBoct";

    protected function setUp()
    {
        Service::setDebugMode(true);
    }

    protected function tearDown()
    {
        Service::setDebugMode(false);
    }

    public function testCreateFromCredentials()
    {
        $objService = Service::createFromCredentials(
            $this->_strClientId,
            $this->_strClientSecret
        );

        $this->assertInstanceOf(
            Service::class,
            $objService
        );
    }

    public function testCreateFromCredentials_Invalid_Credentials()
    {
        $strClientId = "abc";
        $strClientSecret = "def";

        $this->expectException(Exception::class);
        $this->expectExceptionCode(401);

        Service::createFromCredentials(
            $strClientId,
            $strClientSecret
        );
    }

    public function testCreateApplication()
    {
        $objService = Service::createFromCredentials(
            $this->_strClientId,
            $this->_strClientSecret
        );

        $modelPrimaryCompany = ModelCompany::create(
            "Test Primary Company",
            "64564572",
            new DateTime(),
            "00000",
            ModelCompany::LEGAL_STATUS_SOLE_TRADER,
            "Trading Address line 1",
            "Trading Address line 2",
            "Trading Address line 3",
            "Trading Address line 4",
            "AB1 2CD",
            "Registered Address line 1",
            "Registered Address line 2",
            "Registered Address line 3",
            "Registered Address line 4",
            "EF3 4GH",
            "07000 000000",
            "test@email.com",
            "www.test.com",
            "Notes about the primary company",
            null,
            [
                ModelFile::create(
                    "./test.txt",
                    "text/plain",
                    "A test file for the primary company",
                    ModelFile::CATEGORY_AML_CHECKLIST
                )
            ]
        );

        $modelLoan = ModelLoan::create(
            12345,
            "A test for the public library"
        );

        $modelApplicantPerson = ModelPerson::create(
            "Test Forename",
            "Test Middle Name",
            "Test Surname",
            new DateTime(),
            ModelPerson::GENDER_MALE,
            ModelPerson::TITLE_MR,
            "Address line 1",
            "Address line 2",
            "Address line 3",
            "Address line 4",
            "AB1 2CD",
            "07000 000000",
            "07000 000000",
            "test@email.com",
            "Notes about the applicant person",
            ModelCompany::POSITION_DIRECTOR_BIT,
            true,
            [
                ModelFile::create(
                    "./test.txt",
                    "text/plain",
                    "A test file for the applicant person",
                    ModelFile::CATEGORY_GUARANTOR_DETAILS
                )
            ]
        );

        $modelApplicantCompany = ModelCompany::create(
            "Test Applicant Company",
            "43254325",
            new DateTime(),
            "00000, 00001",
            ModelCompany::LEGAL_STATUS_SOLE_TRADER,
            "Trading Address line 1",
            "Trading Address line 2",
            "Trading Address line 3",
            "Trading Address line 4",
            "AB1 2CD",
            "Registered Address line 1",
            "Registered Address line 2",
            "Registered Address line 3",
            "Registered Address line 4",
            "EF3 4GH",
            "07000 000000",
            "test@email.com",
            "www.test.com",
            "Notes about the applicant company",
            ModelCompany::POSITION_PSC_BIT,
            [
                ModelFile::create(
                    "./test.txt",
                    "text/plain",
                    "A test file for the applicant company",
                    ModelFile::CATEGORY_AML_CHECKLIST
                )
            ]
        );

        $intCasePK = $objService->createApplication(
            $modelPrimaryCompany,
            $modelLoan,
            [$modelApplicantPerson, $modelApplicantCompany]
        );
        
        $arrPersonFiles = $modelApplicantPerson->getFiles();
        $this->assertNotNull(
            $arrPersonFiles[0]->getUploadPath()
        );
        
        $arrCompanyFiles = $modelApplicantCompany->getFiles();
        $this->assertNotNull(
            $arrCompanyFiles[0]->getUploadPath()
        );

        $this->assertInternalType(
            "int",
            $intCasePK
        );
    }
}