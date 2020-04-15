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
    private $_strClientId = "2Pduwk0RnkbzAXwlWtFsLtSFQeDdQ1Pu";
    private $_strClientSecret = 
        "LfEUETn0XYx18n8HKHFeE_P5Kwb1PKfUi0-N3ayB7cJBS6ar3n7ujbTnQdjtCe4d";

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

        $arrFiles = [
            ModelFile::create(
                "./test.txt",
                "text/plain",
                "A test file for the primary company",
                ModelFile::CATEGORY_AML_CHECKLIST
            ),
            ModelFile::create(
                "./test.txt",
                "text/plain",
                "A test file for the applicant person",
                ModelFile::CATEGORY_GUARANTOR_DETAILS
            ),
            ModelFile::create(
                "./test.txt",
                "text/plain",
                "A test file for the applicant company",
                ModelFile::CATEGORY_AML_CHECKLIST
            )
        ];

        $modelPrimaryCompany = ModelCompany::create(
            "Test Primary Company",
            "64564566",
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
            [$arrFiles[0]]
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
            null,
            null,
            null,
            null,
            null,
            [$arrFiles[1]]
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
            [$arrFiles[2]]
        );

        $intCasePK = $objService->createApplication(
            $modelPrimaryCompany,
            $modelLoan,
            [$modelApplicantPerson],
            [$modelApplicantCompany]
        );
        
        exit(var_dump($intCasePK));
    }
}