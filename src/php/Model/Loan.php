<?php
declare(strict_types=1);

namespace jlgtechnology\model;

use \Exception as Exception;

class Loan extends AbstractModel
{
    const FIELD_AMOUNT = "FacilityAmount";
    const FIELD_USE    = "FacilityUse";

    public static function create(
        int $intAmount,
        string $strUse
    ) : self
    {
        if ($intAmount < 0) {
            throw new Exception("'$intAmount' is not a valid facility amount");
        }

        return new self(
            [
                self::FIELD_AMOUNT => $intAmount,
                self::FIELD_USE    => $strUse
            ]
        );
    }

    /**
     * Getters
     */
    public static function getFields() : array
    {
        return [
            self::FIELD_AMOUNT,
            self::FIELD_USE
        ];
    }

    public function getAmount() : int
    {
        return $this->_getField(self::FIELD_AMOUNT);
    }

    public function getUse() : string
    {
        return $this->_getField(self::FIELD_USE);
    }
}