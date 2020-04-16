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
    const CATEGORY_LENDER_DOCS = 13;
    const CATEGORY_OLD_FILE_CONTENTS = 18;
    const CATEGORY_APPLICATION_FORMS = 19;
    const CATEGORY_FINAL_FACILITY_AGREEMENTS = 20;
    const CATEGORY_COMPLIANCE_DOCUMENTS = 21;
    const CATEGORY_BUILDING_INSURANCE = 22;
    const CATEGORY_DRAFT_FACILITY_AGREEMENTS = 23;
    const CATEGORY_SOURCING_RESULTS = 24;
    const CATEGORY_ID_AND_PROOF_OF_ADDRESS = 26;
    const CATEGORY_LEGAL_DOCS_DRAFT = 29;
    const CATEGORY_LEGAL_DOCS_FINAL = 30;
    const CATEGORY_PROOF_OF_INCOME = 31;
    const CATEGORY_COMPANY_ACCOUNTS = 32;
    const CATEGORY_MORTGAGE_REFERENCES = 33;
    const CATEGORY_VALUATION_AND_TITLE_PLANS = 34;
    const CATEGORY_E_SIGN = 35;
    const CATEGORY_ACCOUNT_DIRECTOR_LENDING_REPORT = 36;
    const CATEGORY_LENDING_PANEL_REPORT = 37;
    const CATEGORY_FACILITY_PACK = 38;
    const CATEGORY_DEED_OF_GUARANTEE_AND_INDEMNITY = 39;
    const CATEGORY_DEBENTURE = 40;
    const CATEGORY_BOARD_RESOLUTION = 41;
    const CATEGORY_LEGAL_WAIVER_NOTICE = 42;
    const CATEGORY_INDEPENDENT_LEGAL_ADVICE_NOTICE = 43;
    const CATEGORY_BRIDGING_OFFER = 44;
    const CATEGORY_BRIDGING_OFFER_FINAL = 45;
    const CATEGORY_COMPLETION_STATEMENT = 46;
    const CATEGORY_ONLINE_CHAT_MANUSCRIPT = 47;
    const CATEGORY_RECOVERIES = 48;
    const CATEGORY_AML_CHECKLIST = 49;
    const CATEGORY_LOAN_REPAYMENT_ILLUSTRATIONS = 50;
    const CATEGORY_BROKER_DETAILS = 51;
    const CATEGORY_BROKER_COMMISSION_PAYMENTS = 52;
    const CATEGORY_SIMPLE_TASKS = 53;

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

    // A loan's file must have one of these categories
    const LOAN_CATEGORIES = [
        self::CATEGORY_OTHER,
        self::CATEGORY_LENDER_DOCS,
        self::CATEGORY_OLD_FILE_CONTENTS,
        self::CATEGORY_APPLICATION_FORMS,
        self::CATEGORY_FINAL_FACILITY_AGREEMENTS,
        self::CATEGORY_COMPLIANCE_DOCUMENTS,
        self::CATEGORY_BUILDING_INSURANCE,
        self::CATEGORY_DRAFT_FACILITY_AGREEMENTS,
        self::CATEGORY_SOURCING_RESULTS,
        self::CATEGORY_LEGAL_DOCS_DRAFT,
        self::CATEGORY_LEGAL_DOCS_FINAL,
        self::CATEGORY_E_SIGN,
        self::CATEGORY_ACCOUNT_DIRECTOR_LENDING_REPORT,
        self::CATEGORY_LENDING_PANEL_REPORT,
        self::CATEGORY_FACILITY_PACK,
        self::CATEGORY_DEED_OF_GUARANTEE_AND_INDEMNITY,
        self::CATEGORY_DEBENTURE,
        self::CATEGORY_BOARD_RESOLUTION,
        self::CATEGORY_LEGAL_WAIVER_NOTICE,
        self::CATEGORY_INDEPENDENT_LEGAL_ADVICE_NOTICE,
        self::CATEGORY_BRIDGING_OFFER,
        self::CATEGORY_BRIDGING_OFFER_FINAL,
        self::CATEGORY_COMPLETION_STATEMENT,
        self::CATEGORY_ONLINE_CHAT_MANUSCRIPT,
        self::CATEGORY_RECOVERIES,
        self::CATEGORY_LOAN_REPAYMENT_ILLUSTRATIONS,
        self::CATEGORY_BROKER_DETAILS,
        self::CATEGORY_BROKER_COMMISSION_PAYMENTS,
        self::CATEGORY_SIMPLE_TASKS
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