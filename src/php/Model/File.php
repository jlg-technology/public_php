<?php
declare(strict_types=1);

namespace jlgtechnology\model;

use \Exception as Exception;

class File extends AbstractModel
{
    const FIELD_NAME        = "NameAndPath";
    const FIELD_MIME_TYPE   = "MimeType";
    const FIELD_DESCRIPTION = "Description";
    const FIELD_CATEGORY_ID = "CategoryId";
    const FIELD_UPLOAD_PATH = "UploadPath";

    const CATEGORY_SEARCHES = 1;
    const CATEGORY_GUARANTOR_DETAILS = 7;
    const CATEGORY_OTHER = 11;
    const CATEGORY_BANK_STATEMENTS = 12;
    const CATEGORY_ID_AND_PROOF_OF_ADDRESS = 26;
    const CATEGORY_PROOF_OF_INCOME = 31;
    const CATEGORY_COMPANY_ACCOUNTS = 32;
    const CATEGORY_MORTGAGE_REFERENCES = 33;
    const CATEGORY_VALUATION_AND_TITLE_PLANS = 34;
    const CATEGORY_AML_CHECKLIST = 49;

    const CATEGORIES = [
        self::CATEGORY_SEARCHES,
        self::CATEGORY_GUARANTOR_DETAILS,
        self::CATEGORY_OTHER,
        self::CATEGORY_BANK_STATEMENTS,
        self::CATEGORY_ID_AND_PROOF_OF_ADDRESS,
        self::CATEGORY_PROOF_OF_INCOME,
        self::CATEGORY_COMPANY_ACCOUNTS,
        self::CATEGORY_MORTGAGE_REFERENCES,
        self::CATEGORY_VALUATION_AND_TITLE_PLANS,
        self::CATEGORY_AML_CHECKLIST
    ];

    // A company's file must have one of these categories
    const COMPANY_CATEGORIES = [
        self::CATEGORY_SEARCHES,
        self::CATEGORY_OTHER,
        self::CATEGORY_BANK_STATEMENTS,
        self::CATEGORY_COMPANY_ACCOUNTS,
        self::CATEGORY_MORTGAGE_REFERENCES,
        self::CATEGORY_VALUATION_AND_TITLE_PLANS,
        self::CATEGORY_AML_CHECKLIST
    ];

    // A person's file must have one of these categories
    const PERSON_CATEGORIES = [
        self::CATEGORY_SEARCHES,
        self::CATEGORY_GUARANTOR_DETAILS,
        self::CATEGORY_OTHER,
        self::CATEGORY_BANK_STATEMENTS,
        self::CATEGORY_ID_AND_PROOF_OF_ADDRESS,
        self::CATEGORY_PROOF_OF_INCOME,
        self::CATEGORY_MORTGAGE_REFERENCES,
        self::CATEGORY_VALUATION_AND_TITLE_PLANS
    ];

    public static function create(
        string $strName,
        string $strMimeType,
        string $strDescription,
        int $intCategoryId
    ) : self
    {
        if (!file_exists($strName)) {
            throw new Exception("'$strName' doesn't exist");
        }

        if (!is_file($strName)) {
            throw new Exception("'$strName' is not a file");
        }

        if (!is_readable($strName)) {
            throw new Exception("'$strName' is not readable");
        }

        if (!in_array($intCategoryId, self::CATEGORIES)) {
            throw new Exception("'$intCategoryId' is not a valid category Id");
        }

        return new self(
            [
                self::FIELD_NAME        => $strName,
                self::FIELD_MIME_TYPE   => $strMimeType,
                self::FIELD_DESCRIPTION => $strDescription,
                self::FIELD_CATEGORY_ID => $intCategoryId
            ]
        );
    }

    /**
     * Getters
     */
    public static function getFields() : array
    {
        return [
            self::FIELD_NAME,
            self::FIELD_MIME_TYPE,
            self::FIELD_DESCRIPTION,
            self::FIELD_CATEGORY_ID,
            self::FIELD_UPLOAD_PATH
        ];
    }

    public function getNameAndPath() : string
    {
        return $this->_getField(self::FIELD_NAME);
    }

    public function getMimeType() : string
    {
        return $this->_getField(self::FIELD_MIME_TYPE);
    }

    public function getDescription() : string
    {
        return $this->_getField(self::FIELD_DESCRIPTION);
    }

    public function getCategoryId() : int
    {
        return $this->_getField(self::FIELD_CATEGORY_ID);
    }

    public function getUploadPath() : ?string
    {
        return $this->_getField(self::FIELD_UPLOAD_PATH);
    }

    /**
     * Setters
     */
    public function setUploadPath(string $strPath) : self
    {
        return $this->_setField(
            self::FIELD_UPLOAD_PATH,
            $strPath
        );
    }
}