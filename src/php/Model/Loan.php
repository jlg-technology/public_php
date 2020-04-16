<?php
declare(strict_types=1);

namespace jlgtechnology\model;

use \Exception as Exception;

class Loan extends AbstractModel
{
    const FIELD_AMOUNT = "FacilityAmount";
    const FIELD_USE    = "FacilityUse";
    const FIELD_FILES  = "Files";

    public static function create(
        int $intAmount,
        string $strUse,
        ?array $arrModelFiles
    ) : self
    {
        if ($intAmount < 0) {
            throw new Exception("'$intAmount' is not a valid facility amount");
        }

        if (!is_null($arrModelFiles)) {
            foreach ($arrModelFiles as $key => $modelFile) {
                if (!($modelFile instanceof File)) {
                    throw new Exception(
                        "An element of the file array is not a file model - " . 
                            "element is at positon $key with value " . 
                            print_r($modelFile, true)
                    );
                } else if (
                    !in_array(
                        $modelFile->getCategoryId(), 
                        File::LOAN_CATEGORIES
                    )
                ) {
                    throw new Exception(
                        "'" . $modelFile->getCategoryId() . "' " . 
                            "on file '" . $modelFile->getNameAndPath() . "' " .
                            "is not a valid loan file category"
                    );
                }
            }
        }

        return new self(
            [
                self::FIELD_AMOUNT => $intAmount,
                self::FIELD_USE    => $strUse,
                self::FIELD_FILES  => $arrModelFiles
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
            self::FIELD_USE,
            self::FIELD_FILES
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

    public function getFiles() : array
    {
        return $this->_getField(self::FIELD_FILES, []);
    }

    public function addFile(File $modelFile) : self
    {
        if (
            !in_array(
                $modelFile->getCategoryId(), 
                File::LOAN_CATEGORIES
            )
        ) {
            throw new Exception(
                "'" . $modelFile->getCategoryId() . "' " .
                    "is not a valid loan file category"
            );
        }

        $arrModelFiles = $this->_getField(self::FIELD_FILES, []);

        $arrModelFiles[] = $modelFile;

        return $this->_setField(self::FIELD_FILES, $arrModelFiles);
    }
}