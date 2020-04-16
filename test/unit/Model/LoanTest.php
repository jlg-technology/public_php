<?php

namespace jlgtechnology\model;

use PHPUnit\Framework\TestCase;

use Mockery;

use \Exception as Exception;

class LoanTest extends TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testGetFields()
    {
        $this->assertEquals(
            [
                "FacilityAmount",
                "FacilityUse",
                "Files"
            ],
            Loan::getFields()
        );
    }

    public function testCreate()
    {
        $intFacilityAmount = 123;
        $strFacilityUse = "Test";
        $arrFiles = [
            Mockery::mock(File::class)
                ->shouldReceive("getCategoryId")
                ->withNoArgs()
                ->andReturn(File::CATEGORY_OLD_FILE_CONTENTS)
                ->once()
                ->mock()
        ];

        $modelLoan = Loan::create(
            $intFacilityAmount,
            $strFacilityUse,
            $arrFiles
        );

        $this->assertEquals(
            $intFacilityAmount,
            $modelLoan->getAmount()
        );

        $this->assertEquals(
            $strFacilityUse,
            $modelLoan->getUse()
        );

        $this->assertEquals(
            $arrFiles,
            $modelLoan->getFiles()
        );
    }

    public function testCreate_Invalid_Amount()
    {
        $intFacilityAmount = -1;
        $strFacilityUse = "Test";
        $arrFiles = [
            Mockery::mock(File::class)
        ];

        $this->expectException(Exception::class);

        Loan::create(
            $intFacilityAmount,
            $strFacilityUse,
            $arrFiles
        );
    }

    public function testCreate_Invalid_File_Class()
    {
        $intFacilityAmount = 123;
        $strFacilityUse = "Test";
        $arrFiles = [
            Mockery::mock(Loan::class)
        ];

        $this->expectException(Exception::class);

        Loan::create(
            $intFacilityAmount,
            $strFacilityUse,
            $arrFiles
        );
    }

    public function testCreate_Invalid_File_Category()
    {
        $intFacilityAmount = 123;
        $strFacilityUse = "Test";
        $arrFiles = [
            Mockery::mock(File::class)
                ->shouldReceive("getCategoryId")
                ->withNoArgs()
                ->andReturn(File::CATEGORY_VALUATION_AND_TITLE_PLANS)
                ->twice()
                ->shouldReceive("getNameAndPath")
                ->withNoArgs()
                ->andReturn("Test file")
                ->once()
                ->mock()
        ];

        $this->expectException(Exception::class);

        Loan::create(
            $intFacilityAmount,
            $strFacilityUse,
            $arrFiles
        );
    }
}