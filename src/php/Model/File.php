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

    const CATEGORY_ONLINE_CHAT_MANUSCRIPT          = 
        "Online Chat Manuscript";
    const CATEGORY_ID_AND_PROOF_OF_ADDRESS         = 
        "ID & proof of address";
    const CATEGORY_RECOVERIES                      = 
        "Recoveries";
    const CATEGORY_E_SIGN                          = 
        "e-sign";
    const CATEGORY_SIMPLE_TASKS                    = 
        "Simple Tasks";
    const CATEGORY_SEARCHES                        = 
        "Searches";
    const CATEGORY_ACCOUNT_DIRECTOR_LENDING_REPORT = 
        "Account Director Lending Report (ADLR)";
    const CATEGORY_EXECUTIVE_SUMMARY               = 
        "Executive Summary";
    const CATEGORY_C19_SURVEY                      = 
        "C19 survey";
    const CATEGORY_LENDING_PANEL_REPORT            = 
        "Lending Panel Report (LPR)";
    const CATEGORY_FACILITY_PACK                   = 
        "Facility Pack";
    const CATEGORY_DEED_OF_GUARANTEE_AND_INDEMNITY = 
        "Deed of Guarantee & Indemnity (DOGI)";
    const CATEGORY_DEBENTURE                       = 
        "Debenture";
    const CATEGORY_BOARD_RESOLUTION                = 
        "Board Resolution";
    const CATEGORY_LEGAL_WAIVER_NOTICE             = 
        "Legal Waiver Notice";
    const CATEGORY_INDEPENDENT_LEGAL_ADVICE_NOTICE = 
        "Independent Legal Advice Notice";
    const CATEGORY_COMPLETION_STATEMENT            = 
        "Completion Statement";
    const CATEGORY_BANK_STATEMENTS                 = 
        "Bank statements";
    const CATEGORY_AML_DOCUMENTATION               = 
        "AML Documentation";
    const CATEGORY_PROOF_OF_INCOME                 = 
        "Proof of income";
    const CATEGORY_COMPANY_ACCOUNTS                = 
        "Company accounts";
    const CATEGORY_MORTGAGE_REFERENCES             = 
        "Mortgage references";
    const CATEGORY_BRIDGING_OFFER                  = 
        "Bridging Offer";
    const CATEGORY_BRIDGING_OFFER_FINAL            = 
        "Bridging Offer - FINAL";
    const CATEGORY_LOAN_REPAYMENT_ILLUSTRATIONS    = 
        "Loan Repayment Illustrations";
    const CATEGORY_BROKER_DETAILS                  = 
        "Broker Details";
    const CATEGORY_GUARANTOR_DETAILS               = 
        "Guarantor Details";
    const CATEGORY_SOURCING_RESULTS                = 
        "Sourcing Results";
    const CATEGORY_APPLICATION_FORMS               = 
        "Application Forms";
    const CATEGORY_LENDER_DOCS                     = 
        "Lender docs";
    const CATEGORY_VALUATION_AND_TITLE_PLANS       = 
        "Valuation and title plans";
    const CATEGORY_COMPLIANCE_DOCUMENTS            = 
        "Compliance Documents";
    const CATEGORY_BUILDING_INSURANCE              = 
        "Building Insurance";
    const CATEGORY_DRAFT_FACILITY_AGREEMENTS       = 
        "Draft facility agreements";
    const CATEGORY_FINAL_FACILITY_AGREEMENTS       = 
        "Final facility agreements";
    const CATEGORY_OLD_FILE_CONTENTS               = 
        "Old File Contents";
    const CATEGORY_LEGAL_DOCS_DRAFT                = 
        "Legal docs draft (charge, guarantees, debentures)";
    const CATEGORY_COMPLAINTS                      = 
        "Complaints ";
    const CATEGORY_LEGAL_DOCS_FINAL                = 
        "Legal docs final (charges, debenture, guarantees)";
    const CATEGORY_OTHER                           = 
        "Other";
    const CATEGORY_BROKER_COMMISSION_PAYMENTS      = 
        "Broker Commission Payments";

    const CATEGORIES = [
        self::CATEGORY_ONLINE_CHAT_MANUSCRIPT,
        self::CATEGORY_ID_AND_PROOF_OF_ADDRESS,
        self::CATEGORY_RECOVERIES,
        self::CATEGORY_E_SIGN,
        self::CATEGORY_SIMPLE_TASKS,
        self::CATEGORY_SEARCHES,
        self::CATEGORY_ACCOUNT_DIRECTOR_LENDING_REPORT,
        self::CATEGORY_EXECUTIVE_SUMMARY,
        self::CATEGORY_C19_SURVEY,
        self::CATEGORY_LENDING_PANEL_REPORT,
        self::CATEGORY_FACILITY_PACK,
        self::CATEGORY_DEED_OF_GUARANTEE_AND_INDEMNITY,
        self::CATEGORY_DEBENTURE,
        self::CATEGORY_BOARD_RESOLUTION,
        self::CATEGORY_LEGAL_WAIVER_NOTICE,
        self::CATEGORY_INDEPENDENT_LEGAL_ADVICE_NOTICE,
        self::CATEGORY_COMPLETION_STATEMENT,
        self::CATEGORY_BANK_STATEMENTS,
        self::CATEGORY_AML_DOCUMENTATION,
        self::CATEGORY_PROOF_OF_INCOME,
        self::CATEGORY_COMPANY_ACCOUNTS,
        self::CATEGORY_MORTGAGE_REFERENCES,
        self::CATEGORY_BRIDGING_OFFER,
        self::CATEGORY_BRIDGING_OFFER_FINAL,
        self::CATEGORY_LOAN_REPAYMENT_ILLUSTRATIONS,
        self::CATEGORY_BROKER_DETAILS,
        self::CATEGORY_GUARANTOR_DETAILS,
        self::CATEGORY_SOURCING_RESULTS,
        self::CATEGORY_APPLICATION_FORMS,
        self::CATEGORY_LENDER_DOCS,
        self::CATEGORY_VALUATION_AND_TITLE_PLANS,
        self::CATEGORY_COMPLIANCE_DOCUMENTS,
        self::CATEGORY_BUILDING_INSURANCE,
        self::CATEGORY_DRAFT_FACILITY_AGREEMENTS,
        self::CATEGORY_FINAL_FACILITY_AGREEMENTS,
        self::CATEGORY_OLD_FILE_CONTENTS,
        self::CATEGORY_LEGAL_DOCS_DRAFT,
        self::CATEGORY_COMPLAINTS,
        self::CATEGORY_LEGAL_DOCS_FINAL,
        self::CATEGORY_OTHER,
        self::CATEGORY_BROKER_COMMISSION_PAYMENTS
    ];

    // A company's file must have one of these categories
    const COMPANY_CATEGORIES = [
        self::CATEGORY_SEARCHES,
        self::CATEGORY_EXECUTIVE_SUMMARY,
        self::CATEGORY_C19_SURVEY,
        self::CATEGORY_BANK_STATEMENTS,
        self::CATEGORY_AML_DOCUMENTATION,
        self::CATEGORY_COMPANY_ACCOUNTS,
        self::CATEGORY_MORTGAGE_REFERENCES,
        self::CATEGORY_VALUATION_AND_TITLE_PLANS,
        self::CATEGORY_COMPLAINTS,
        self::CATEGORY_OTHER
    ];

    // A person's file must have one of these categories
    const PERSON_CATEGORIES = [
        self::CATEGORY_ID_AND_PROOF_OF_ADDRESS,
        self::CATEGORY_SEARCHES,
        self::CATEGORY_BANK_STATEMENTS,
        self::CATEGORY_PROOF_OF_INCOME,
        self::CATEGORY_MORTGAGE_REFERENCES,
        self::CATEGORY_GUARANTOR_DETAILS,
        self::CATEGORY_VALUATION_AND_TITLE_PLANS,
        self::CATEGORY_OTHER
    ];

    // A loan's file must have one of these categories
    const LOAN_CATEGORIES = [
        self::CATEGORY_ONLINE_CHAT_MANUSCRIPT,
        self::CATEGORY_RECOVERIES,
        self::CATEGORY_E_SIGN,
        self::CATEGORY_SIMPLE_TASKS,
        self::CATEGORY_ACCOUNT_DIRECTOR_LENDING_REPORT,
        self::CATEGORY_LENDING_PANEL_REPORT,
        self::CATEGORY_FACILITY_PACK,
        self::CATEGORY_DEED_OF_GUARANTEE_AND_INDEMNITY,
        self::CATEGORY_DEBENTURE,
        self::CATEGORY_BOARD_RESOLUTION,
        self::CATEGORY_LEGAL_WAIVER_NOTICE,
        self::CATEGORY_INDEPENDENT_LEGAL_ADVICE_NOTICE,
        self::CATEGORY_COMPLETION_STATEMENT,
        self::CATEGORY_BRIDGING_OFFER,
        self::CATEGORY_BRIDGING_OFFER_FINAL,
        self::CATEGORY_LOAN_REPAYMENT_ILLUSTRATIONS,
        self::CATEGORY_BROKER_DETAILS,
        self::CATEGORY_SOURCING_RESULTS,
        self::CATEGORY_APPLICATION_FORMS,
        self::CATEGORY_LENDER_DOCS,
        self::CATEGORY_COMPLIANCE_DOCUMENTS,
        self::CATEGORY_BUILDING_INSURANCE,
        self::CATEGORY_DRAFT_FACILITY_AGREEMENTS,
        self::CATEGORY_FINAL_FACILITY_AGREEMENTS,
        self::CATEGORY_OLD_FILE_CONTENTS,
        self::CATEGORY_LEGAL_DOCS_DRAFT,
        self::CATEGORY_LEGAL_DOCS_FINAL,
        self::CATEGORY_OTHER,
        self::CATEGORY_BROKER_COMMISSION_PAYMENTS
    ];

    public static function create(
        string $strName,
        string $strMimeType,
        string $strDescription,
        string $strCategoryId
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

        if (!in_array($strCategoryId, self::CATEGORIES)) {
            throw new Exception("'$strCategoryId' is not a valid category Id");
        }

        return new self(
            [
                self::FIELD_NAME        => $strName,
                self::FIELD_MIME_TYPE   => $strMimeType,
                self::FIELD_DESCRIPTION => $strDescription,
                self::FIELD_CATEGORY_ID => $strCategoryId
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

    public function getCategoryId() : string
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