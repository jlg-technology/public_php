<?php

namespace jlgtechnology\model;

use PHPUnit\Framework\TestCase;

use Mockery;

use \DateTime as DateTime;

use \Exception as Exception;

class PersonTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetFields()
    {
        $this->assertEquals(
            [
                "Forename",
                "MiddleName",
                "Surname",
                "DoB",
                "Gender",
                "Title",
                "Address1",
                "Address2",
                "Address3",
                "Address4",
                "AddressPostcode",
                "DayPhone",
                "MobilePhone",
                "Email",
                "Notes",
                "Position",
                "PrimaryContact",
                "Files",
                "PassportForename",
                "PassportMiddleName",
                "PassportSurname"
            ],
            Person::getFields()
        );
    }

    public function testCreate()
    {
        $strForename         = "Test 1";
        $strMiddleName       = "Test 2";
        $strSurname          = "Test 3";
        $datetimeDateOfBirth = new DateTime();
        $intGender           = Person::GENDER_MALE;
        $strTitle            = Person::TITLE_MR;
        $strAddressOne       = "Test 4";
        $strAddressTwo       = "Test 5";
        $strAddressThree     = "Test 6";
        $strAddressFour      = "Test 7";
        $strAddressPostcode  = "AB1 2CD";
        $strDayPhone         = "07000 000000";
        $strMobilePhone      = "07000 000000";
        $strEmail            = "test@email.com";
        $strNotes            = "Test 8";
        $intPosition         = Person::POSITION_DIRECTOR_BIT;
        $boolPrimaryContact  = true;
        $arrModelFiles       = [
            Mockery::mock(File::class)
                ->shouldReceive("getCategoryId")
                ->withNoArgs()
                ->andReturn(File::CATEGORY_GUARANTOR_DETAILS)
                ->once()
                ->mock()
        ];
        $strPassportForename = "Forename";
        $strPassportMiddleName = "MiddleName";
        $strPassportSurname = "Surname";

        $modelPerson = Person::create(
            $strForename,
            $strMiddleName,
            $strSurname,
            $datetimeDateOfBirth,
            $intGender,
            $strTitle,
            $strAddressOne,
            $strAddressTwo,
            $strAddressThree,
            $strAddressFour,
            $strAddressPostcode,
            $strDayPhone,
            $strMobilePhone,
            $strEmail,
            $strNotes,
            $intPosition,
            $boolPrimaryContact,
            $arrModelFiles,
            $strPassportForename,
            $strPassportMiddleName,
            $strPassportSurname
        );

        $this->assertEquals(
            $strForename,
            $modelPerson->getForename()
        );

        $this->assertEquals(
            $strMiddleName,
            $modelPerson->getMiddleName()
        );

        $this->assertEquals(
            $strSurname,
            $modelPerson->getSurname()
        );

        $this->assertEquals(
            $strPassportForename,
            $modelPerson->getPassportForename()
        );

        $this->assertEquals(
            $strPassportMiddleName,
            $modelPerson->getPassportMiddleName()
        );

        $this->assertEquals(
            $strPassportSurname,
            $modelPerson->getPassportSurname()
        );

        $this->assertEquals(
            $datetimeDateOfBirth->getTimestamp(),
            $modelPerson->getDateOfBirth()->getTimestamp()
        );

        $this->assertEquals(
            $intGender,
            $modelPerson->getGender()
        );

        $this->assertEquals(
            $strTitle,
            $modelPerson->getTitle()
        );

        $this->assertEquals(
            $strAddressOne,
            $modelPerson->getAddressLine1()
        );

        $this->assertEquals(
            $strAddressTwo,
            $modelPerson->getAddressLine2()
        );

        $this->assertEquals(
            $strAddressThree,
            $modelPerson->getAddressLine3()
        );

        $this->assertEquals(
            $strAddressFour,
            $modelPerson->getAddressLine4()
        );

        $this->assertEquals(
            $strAddressPostcode,
            $modelPerson->getAddressPostcode()
        );

        $this->assertEquals(
            $strDayPhone,
            $modelPerson->getDayPhone()
        );

        $this->assertEquals(
            $strMobilePhone,
            $modelPerson->getMobilePhone()
        );

        $this->assertEquals(
            $strEmail,
            $modelPerson->getEmail()
        );

        $this->assertEquals(
            $strNotes,
            $modelPerson->getNotes()
        );

        $this->assertEquals(
            $intPosition,
            $modelPerson->getPosition()
        );

        $this->assertEquals(
            $boolPrimaryContact,
            $modelPerson->getIsPrimaryContact()
        );

        $this->assertEquals(
            $arrModelFiles,
            $modelPerson->getFiles()
        );
    }

    public function testCreate_Invalid_Gender()
    {
        $strForename         = "Test 1";
        $strMiddleName       = "Test 2";
        $strSurname          = "Test 3";
        $datetimeDateOfBirth = new DateTime();
        $intGender           = 999;
        $strTitle            = Person::TITLE_MR;
        $strAddressOne       = "Test 4";
        $strAddressTwo       = "Test 5";
        $strAddressThree     = "Test 6";
        $strAddressFour      = "Test 7";
        $strAddressPostcode  = "AB1 2CD";
        $strDayPhone         = "07000 000000";
        $strMobilePhone      = "07000 000000";
        $strEmail            = "test@email.com";
        $strNotes            = "Test 8";
        $intPosition         = Person::POSITION_DIRECTOR_BIT;
        $boolPrimaryContact  = true;
        $arrModelFiles       = [
            Mockery::mock(File::class)
        ];
        $strPassportForename = "Forename";
        $strPassportMiddleName = "MiddleName";
        $strPassportSurname = "Surname";

        $this->expectException(Exception::class);

        $modelPerson = Person::create(
            $strForename,
            $strMiddleName,
            $strSurname,
            $datetimeDateOfBirth,
            $intGender,
            $strTitle,
            $strAddressOne,
            $strAddressTwo,
            $strAddressThree,
            $strAddressFour,
            $strAddressPostcode,
            $strDayPhone,
            $strMobilePhone,
            $strEmail,
            $strNotes,
            $intPosition,
            $boolPrimaryContact,
            $arrModelFiles,
            $strPassportForename,
            $strPassportMiddleName,
            $strPassportSurname
        );
    }

    public function testCreate_Invalid_Title()
    {
        $strForename         = "Test 1";
        $strMiddleName       = "Test 2";
        $strSurname          = "Test 3";
        $datetimeDateOfBirth = new DateTime();
        $intGender           = Person::GENDER_MALE;
        $strTitle            = "abc";
        $strAddressOne       = "Test 4";
        $strAddressTwo       = "Test 5";
        $strAddressThree     = "Test 6";
        $strAddressFour      = "Test 7";
        $strAddressPostcode  = "AB1 2CD";
        $strDayPhone         = "07000 000000";
        $strMobilePhone      = "07000 000000";
        $strEmail            = "test@email.com";
        $strNotes            = "Test 8";
        $intPosition         = Person::POSITION_DIRECTOR_BIT;
        $boolPrimaryContact  = true;
        $arrModelFiles       = [
            Mockery::mock(File::class)
        ];
        $strPassportForename = "Forename";
        $strPassportMiddleName = "MiddleName";
        $strPassportSurname = "Surname";

        $this->expectException(Exception::class);

        $modelPerson = Person::create(
            $strForename,
            $strMiddleName,
            $strSurname,
            $datetimeDateOfBirth,
            $intGender,
            $strTitle,
            $strAddressOne,
            $strAddressTwo,
            $strAddressThree,
            $strAddressFour,
            $strAddressPostcode,
            $strDayPhone,
            $strMobilePhone,
            $strEmail,
            $strNotes,
            $intPosition,
            $boolPrimaryContact,
            $arrModelFiles,
            $strPassportForename,
            $strPassportMiddleName,
            $strPassportSurname
        );
    }

    public function testCreate_Invalid_Postcode()
    {
        $strForename         = "Test 1";
        $strMiddleName       = "Test 2";
        $strSurname          = "Test 3";
        $datetimeDateOfBirth = new DateTime();
        $intGender           = Person::GENDER_MALE;
        $strTitle            = Person::TITLE_MR;
        $strAddressOne       = "Test 4";
        $strAddressTwo       = "Test 5";
        $strAddressThree     = "Test 6";
        $strAddressFour      = "Test 7";
        $strAddressPostcode  = "abc";
        $strDayPhone         = "07000 000000";
        $strMobilePhone      = "07000 000000";
        $strEmail            = "test@email.com";
        $strNotes            = "Test 8";
        $intPosition         = Person::POSITION_DIRECTOR_BIT;
        $boolPrimaryContact  = true;
        $arrModelFiles       = [
            Mockery::mock(File::class)
        ];
        $strPassportForename = "Forename";
        $strPassportMiddleName = "MiddleName";
        $strPassportSurname = "Surname";

        $this->expectException(Exception::class);

        $modelPerson = Person::create(
            $strForename,
            $strMiddleName,
            $strSurname,
            $datetimeDateOfBirth,
            $intGender,
            $strTitle,
            $strAddressOne,
            $strAddressTwo,
            $strAddressThree,
            $strAddressFour,
            $strAddressPostcode,
            $strDayPhone,
            $strMobilePhone,
            $strEmail,
            $strNotes,
            $intPosition,
            $boolPrimaryContact,
            $arrModelFiles,
            $strPassportForename,
            $strPassportMiddleName,
            $strPassportSurname
        );
    }

    public function testCreate_Invalid_Position()
    {
        $strForename         = "Test 1";
        $strMiddleName       = "Test 2";
        $strSurname          = "Test 3";
        $datetimeDateOfBirth = new DateTime();
        $intGender           = Person::GENDER_MALE;
        $strTitle            = Person::TITLE_MR;
        $strAddressOne       = "Test 4";
        $strAddressTwo       = "Test 5";
        $strAddressThree     = "Test 6";
        $strAddressFour      = "Test 7";
        $strAddressPostcode  = "AB1 2CD";
        $strDayPhone         = "07000 000000";
        $strMobilePhone      = "07000 000000";
        $strEmail            = "test@email.com";
        $strNotes            = "Test 8";
        $intPosition         = 999;
        $boolPrimaryContact  = true;
        $arrModelFiles       = [
            Mockery::mock(File::class)
        ];
        $strPassportForename = "Forename";
        $strPassportMiddleName = "MiddleName";
        $strPassportSurname = "Surname";

        $this->expectException(Exception::class);

        $modelPerson = Person::create(
            $strForename,
            $strMiddleName,
            $strSurname,
            $datetimeDateOfBirth,
            $intGender,
            $strTitle,
            $strAddressOne,
            $strAddressTwo,
            $strAddressThree,
            $strAddressFour,
            $strAddressPostcode,
            $strDayPhone,
            $strMobilePhone,
            $strEmail,
            $strNotes,
            $intPosition,
            $boolPrimaryContact,
            $arrModelFiles,
            $strPassportForename,
            $strPassportMiddleName,
            $strPassportSurname
        );
    }

    public function testCreate_Invalid_File_Class()
    {
        $strForename         = "Test 1";
        $strMiddleName       = "Test 2";
        $strSurname          = "Test 3";
        $datetimeDateOfBirth = new DateTime();
        $intGender           = Person::GENDER_MALE;
        $strTitle            = Person::TITLE_MR;
        $strAddressOne       = "Test 4";
        $strAddressTwo       = "Test 5";
        $strAddressThree     = "Test 6";
        $strAddressFour      = "Test 7";
        $strAddressPostcode  = "AB1 2CD";
        $strDayPhone         = "07000 000000";
        $strMobilePhone      = "07000 000000";
        $strEmail            = "test@email.com";
        $strNotes            = "Test 8";
        $intPosition         = Person::POSITION_DIRECTOR_BIT;
        $boolPrimaryContact  = true;
        $arrModelFiles       = [
            Mockery::mock(Loan::class)
        ];
        $strPassportForename = "Forename";
        $strPassportMiddleName = "MiddleName";
        $strPassportSurname = "Surname";


        $this->expectException(Exception::class);

        $modelPerson = Person::create(
            $strForename,
            $strMiddleName,
            $strSurname,
            $datetimeDateOfBirth,
            $intGender,
            $strTitle,
            $strAddressOne,
            $strAddressTwo,
            $strAddressThree,
            $strAddressFour,
            $strAddressPostcode,
            $strDayPhone,
            $strMobilePhone,
            $strEmail,
            $strNotes,
            $intPosition,
            $boolPrimaryContact,
            $arrModelFiles,
            $strPassportForename,
            $strPassportMiddleName,
            $strPassportSurname
        );
    }

    public function testCreate_Invalid_File_Category()
    {
        $strForename         = "Test 1";
        $strMiddleName       = "Test 2";
        $strSurname          = "Test 3";
        $datetimeDateOfBirth = new DateTime();
        $intGender           = Person::GENDER_MALE;
        $strTitle            = Person::TITLE_MR;
        $strAddressOne       = "Test 4";
        $strAddressTwo       = "Test 5";
        $strAddressThree     = "Test 6";
        $strAddressFour      = "Test 7";
        $strAddressPostcode  = "AB1 2CD";
        $strDayPhone         = "07000 000000";
        $strMobilePhone      = "07000 000000";
        $strEmail            = "test@email.com";
        $strNotes            = "Test 8";
        $intPosition         = Person::POSITION_DIRECTOR_BIT;
        $boolPrimaryContact  = true;
        $arrModelFiles       = [
            Mockery::mock(File::class)
                ->shouldReceive("getCategoryId")
                ->withNoArgs()
                ->andReturn(File::CATEGORY_COMPANY_ACCOUNTS)
                ->twice()
                ->shouldReceive("getNameAndPath")
                ->withNoArgs()
                ->andReturn("Test")
                ->once()
                ->mock()
        ];
        $strPassportForename = "Forename";
        $strPassportMiddleName = "MiddleName";
        $strPassportSurname = "Surname";

        $this->expectException(Exception::class);

        $modelPerson = Person::create(
            $strForename,
            $strMiddleName,
            $strSurname,
            $datetimeDateOfBirth,
            $intGender,
            $strTitle,
            $strAddressOne,
            $strAddressTwo,
            $strAddressThree,
            $strAddressFour,
            $strAddressPostcode,
            $strDayPhone,
            $strMobilePhone,
            $strEmail,
            $strNotes,
            $intPosition,
            $boolPrimaryContact,
            $arrModelFiles,
            $strPassportForename,
            $strPassportMiddleName,
            $strPassportSurname
        );
    }

    public function testAddFile()
    {
        $strForename         = "Test 1";
        $strMiddleName       = "Test 2";
        $strSurname          = "Test 3";
        $datetimeDateOfBirth = new DateTime();
        $intGender           = Person::GENDER_MALE;
        $strTitle            = Person::TITLE_MR;
        $strAddressOne       = "Test 4";
        $strAddressTwo       = "Test 5";
        $strAddressThree     = "Test 6";
        $strAddressFour      = "Test 7";
        $strAddressPostcode  = "AB1 2CD";
        $strDayPhone         = "07000 000000";
        $strMobilePhone      = "07000 000000";
        $strEmail            = "test@email.com";
        $strNotes            = "Test 8";
        $intPosition         = Person::POSITION_DIRECTOR_BIT;
        $boolPrimaryContact  = true;
        $arrModelFiles       = [];
        $strPassportForename = "Forename";
        $strPassportMiddleName = "MiddleName";
        $strPassportSurname = "Surname";

        $modelPerson = Person::create(
            $strForename,
            $strMiddleName,
            $strSurname,
            $datetimeDateOfBirth,
            $intGender,
            $strTitle,
            $strAddressOne,
            $strAddressTwo,
            $strAddressThree,
            $strAddressFour,
            $strAddressPostcode,
            $strDayPhone,
            $strMobilePhone,
            $strEmail,
            $strNotes,
            $intPosition,
            $boolPrimaryContact,
            $arrModelFiles,
            $strPassportForename,
            $strPassportMiddleName,
            $strPassportSurname
        );

        $this->assertEquals(
            [],
            $modelPerson->getFiles()
        );

        $mockModelFile = Mockery::mock(File::class)
            ->shouldReceive("getCategoryId")
            ->withNoArgs()
            ->andReturn(File::CATEGORY_GUARANTOR_DETAILS)
            ->once()
            ->mock();

        $modelPerson->addFile($mockModelFile);

        $this->assertEquals(
            [$mockModelFile],
            $modelPerson->getFiles()
        );
    }

    public function testAddFile_Invalid_Category_Id()
    {
        $strForename         = "Test 1";
        $strMiddleName       = "Test 2";
        $strSurname          = "Test 3";
        $datetimeDateOfBirth = new DateTime();
        $intGender           = Person::GENDER_MALE;
        $strTitle            = Person::TITLE_MR;
        $strAddressOne       = "Test 4";
        $strAddressTwo       = "Test 5";
        $strAddressThree     = "Test 6";
        $strAddressFour      = "Test 7";
        $strAddressPostcode  = "AB1 2CD";
        $strDayPhone         = "07000 000000";
        $strMobilePhone      = "07000 000000";
        $strEmail            = "test@email.com";
        $strNotes            = "Test 8";
        $intPosition         = Person::POSITION_DIRECTOR_BIT;
        $boolPrimaryContact  = true;
        $arrModelFiles       = [];
        $strPassportForename = "Forename";
        $strPassportMiddleName = "MiddleName";
        $strPassportSurname = "Surname";


        $modelPerson = Person::create(
            $strForename,
            $strMiddleName,
            $strSurname,
            $datetimeDateOfBirth,
            $intGender,
            $strTitle,
            $strAddressOne,
            $strAddressTwo,
            $strAddressThree,
            $strAddressFour,
            $strAddressPostcode,
            $strDayPhone,
            $strMobilePhone,
            $strEmail,
            $strNotes,
            $intPosition,
            $boolPrimaryContact,
            $arrModelFiles,
            $strPassportForename,
            $strPassportMiddleName,
            $strPassportSurname
        );

        $this->assertEquals(
            [],
            $modelPerson->getFiles()
        );

        $mockModelFile = Mockery::mock(File::class)
            ->shouldReceive("getCategoryId")
            ->withNoArgs()
            ->andReturn(File::CATEGORY_COMPANY_ACCOUNTS)
            ->twice()
            ->mock();

        $this->expectException(Exception::class);

        $modelPerson->addFile($mockModelFile);
    }
}