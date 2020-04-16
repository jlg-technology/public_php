<?php

namespace jlgtechnology\model;

use PHPUnit\Framework\TestCase;

class LoanTest extends TestCase
{
    public function testGetFields()
    {
        $this->assertEquals(
            [
                "FacilityAmount",
                "FacilityUse"
            ],
            Loan::getFields()
        );
    }

    public function testCreate()
    {
        $intFacilityAmount = 123;
        $strFacilityUse = "Test";

        $modelLoan = Loan::create(
            $intFacilityAmount,
            $strFacilityUse
        );

        $this->assertEquals(
            $intFacilityAmount,
            $modelLoan->getAmount()
        );

        $this->assertEquals(
            $strFacilityUse,
            $modelLoan->getUse()
        );
    }
}