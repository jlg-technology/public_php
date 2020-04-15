<?php
declare(strict_types=1);

namespace jlgtechnology\model;

use \DateTime as DateTime;

use \Exception as Exception;

class Company extends AbstractModel
{
    const FIELD_NAME                        = "Name";
    const FIELD_COMPANY_REGISTRATION_NUMBER = "CRN";
    const FIELD_INCORPORATION_DATE          = "IncorporationDate";
    const FIELD_SIC_CODES                   = "SICCodes";
    const FIELD_LEGAL_STATUS                = "LegalStatus";
    const FIELD_TRADING_ADDRESS_LINE_1      = "TradingAddress1";
    const FIELD_TRADING_ADDRESS_LINE_2      = "TradingAddress2";
    const FIELD_TRADING_ADDRESS_LINE_3      = "TradingAddress3";
    const FIELD_TRADING_ADDRESS_LINE_4      = "TradingAddress4";
    const FIELD_TRADING_ADDRESS_POSTCODE    = "TradingAddressPostcode";
    const FIELD_REGISTERED_ADDRESS_LINE_1   = "RegisteredAddress1";
    const FIELD_REGISTERED_ADDRESS_LINE_2   = "RegisteredAddress2";
    const FIELD_REGISTERED_ADDRESS_LINE_3   = "RegisteredAddress3";
    const FIELD_REGISTERED_ADDRESS_LINE_4   = "RegisteredAddress4";
    const FIELD_REGISTERED_ADDRESS_POSTCODE = "RegisteredAddressPostcode";
    const FIELD_TELEPHONE                   = "Telephone";
    const FIELD_EMAIL                       = "Email";
    const FIELD_WEBSITE                     = "Website";
    const FIELD_NOTES                       = "Notes";
    const FIELD_POSITION                    = "Position";
    const FIELD_FILES                       = "Files";

    const LEGAL_STATUS_SOLE_TRADER = 0;
    const LEGAL_STATUS_LIMITED_LIABILITY_PARTNERSHIP = 1;
    const LEGAL_STATUS_ORDINARY_PARTNERSHIP = 2;
    const LEGAL_STATUS_LIMITED_COMPANY = 3;
    const LEGAL_STATUS_PUBLIC_LIMITED_COMPANY = 4;
    const LEGAL_STATUS_CHARITY = 5;

    const POSITION_DIRECTOR_BIT   = 1;
    const POSITION_GUARANTOR_BIT  = 2;
    const POSITION_PSC_BIT        = 4;
    const POSITION_NO_CONTACT_BIT = 8;

    const COMPANY_REGISTRATION_NUMBER_REGEX = 
        "/^(([0-9]{8})|([A-Z]{2}[0-9]{6})|(R[0-9]{7}))$/i";
    const TELEPHONE_REGEX = 
        "/^(?:(?:\(?(?:0(?:0|11)\)?[\s-]?\(?|\+)44\)?[\s-]?" .
        "(?:\(?0\)?[\s-]?)?)|(?:\(?0))(?:(?:\d{5}\)?[\s-]?\d{4,5})" .
        "|(?:\d{4}\)?[\s-]?(?:\d{5}|\d{3}[\s-]?\d{3}))|(?:\d{3}\)?" .
        "[\s-]?\d{3}[\s-]?\d{3,4})|(?:\d{2}\)?[\s-]?\d{4}[\s-]?\d{4}))$/";
    const POSTCODE_REGEX = 
        "/^\s*((([A-Z]{1,2}[0-9][A-Z0-9]?" .
        "|ASCN|STHL|TDCU|BBND|[BFS]IQQ|PCRN|TKCA) ?[0-9][A-Z]{2}|BFPO ?" .
        "[0-9]{1,4}|(KY[0-9]|MSR|VG|AI)[ -]?[0-9]{4}|[A-Z]{2} ?[0-9]{2}|GE ?" .
        "CX|GIR ?0A{2}|SAN ?TA1))\s*$/i";
    const SIC_CODES_REGEX = "/^\d{5}(,\s?\d{5})*$/";

    private const INCORPORATION_DATE_FORMAT = "Y-m-d H:i:s";

    public static function create(
        string $strName,
        string $strCRN,
        DateTime $datetimeIncorporationDate,
        ?string $strSicCodes,
        ?int $intLegalStatus,
        ?string $strTradingAddressOne,
        ?string $strTradingAddressTwo,
        ?string $strTradingAddressThree,
        ?string $strTradingAddressFour,
        ?string $strTradingAddressPostcode,
        ?string $strRegisteredAddressOne,
        ?string $strRegisteredAddressTwo,
        ?string $strRegisteredAddressThree,
        ?string $strRegisteredAddressFour,
        ?string $strRegisteredAddressPostcode,
        ?string $strTelephone,
        ?string $strEmail,
        ?string $strWebsite,
        ?string $strNotes,
        ?int $intPosition,
        ?array $arrModelFiles
    ) : self
    {
        if (!preg_match(self::COMPANY_REGISTRATION_NUMBER_REGEX, $strCRN)) {
            throw new Exception(
                "'$strCRN' is not a valid company registration number"
            );
        }

        if (
            !is_null($strSicCodes) &&
            !preg_match(self::SIC_CODES_REGEX, $strSicCodes)
        ) {
            throw new Exception(
                "'$strSicCodes' is not a valid comma seperated list " .
                    "of sic codes"
            );
        }

        if (
            !is_null($intLegalStatus) &&
            !in_array(
                $intLegalStatus,
                [
                    self::LEGAL_STATUS_SOLE_TRADER,
                    self::LEGAL_STATUS_LIMITED_LIABILITY_PARTNERSHIP,
                    self::LEGAL_STATUS_ORDINARY_PARTNERSHIP,
                    self::LEGAL_STATUS_LIMITED_COMPANY,
                    self::LEGAL_STATUS_PUBLIC_LIMITED_COMPANY,
                    self::LEGAL_STATUS_CHARITY
                ]
            )
        ) {
            throw new Exception(
                "'$intLegalStatus' is not a valid legal status id"
            );
        }

        if (
            !is_null($strTradingAddressPostcode) &&
            !preg_match(self::POSTCODE_REGEX, $strTradingAddressPostcode)
        ) {
            throw new Exception(
                "'$strTradingAddressPostcode' is not a valid UK postcode"
            );
        }

        if (
            !is_null($strRegisteredAddressPostcode) &&
            !preg_match(self::POSTCODE_REGEX, $strRegisteredAddressPostcode)
        ) {
            throw new Exception(
                "'$strRegisteredAddressPostcode' is not a valid UK postcode"
            );
        }

        if (
            !is_null($strTelephone) &&
            !preg_match(self::TELEPHONE_REGEX, $strTelephone)
        ) {
            throw new Exception(
                "'$strTelephone' is not a valid UK phone number"
            );
        }

        if (
            !is_null($strEmail) &&
            !filter_var($strEmail, FILTER_VALIDATE_EMAIL)
        ) {
            throw new Exception(
                "'$strEmail' is not a valid UK phone number"
            );
        }

        if (
            !is_null($intPosition) &&
            (
                $intPosition < 0 ||
                $intPosition > (
                    self::POSITION_DIRECTOR_BIT + 
                    self::POSITION_GUARANTOR_BIT +
                    self::POSITION_PSC_BIT +
                    self::POSITION_NO_CONTACT_BIT
                )
            )
        ) {
            throw new Exception("'$intPosition' is not a valid position");
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
                        File::COMPANY_CATEGORIES
                    )
                ) {
                    throw new Exception(
                        "'" . $modelFile->getCategoryId() . "' " . 
                            "on file '" . $modelFile->getNameAndPath() . "' " .
                            "is not a valid company category"
                    );
                }
            }
        }

        return new self(
            [
                self::FIELD_NAME                        => 
                    $strName,
                self::FIELD_COMPANY_REGISTRATION_NUMBER => 
                    $strCRN,
                self::FIELD_INCORPORATION_DATE          => 
                    $datetimeIncorporationDate->format(
                        self::INCORPORATION_DATE_FORMAT
                    ),
                self::FIELD_SIC_CODES                   => 
                    $strSicCodes,
                self::FIELD_LEGAL_STATUS                => 
                    $intLegalStatus,
                self::FIELD_TRADING_ADDRESS_LINE_1      => 
                    $strTradingAddressOne,
                self::FIELD_TRADING_ADDRESS_LINE_2      => 
                    $strTradingAddressTwo,
                self::FIELD_TRADING_ADDRESS_LINE_3      => 
                    $strTradingAddressThree,
                self::FIELD_TRADING_ADDRESS_LINE_4      => 
                    $strTradingAddressFour,
                self::FIELD_TRADING_ADDRESS_POSTCODE    => 
                    $strTradingAddressPostcode,
                self::FIELD_REGISTERED_ADDRESS_LINE_1   => 
                    $strRegisteredAddressOne,
                self::FIELD_REGISTERED_ADDRESS_LINE_2   => 
                    $strRegisteredAddressTwo,
                self::FIELD_REGISTERED_ADDRESS_LINE_3   => 
                    $strRegisteredAddressThree,
                self::FIELD_REGISTERED_ADDRESS_LINE_4   => 
                    $strRegisteredAddressFour,
                self::FIELD_REGISTERED_ADDRESS_POSTCODE => 
                    $strRegisteredAddressPostcode,
                self::FIELD_TELEPHONE                   => 
                    $strTelephone,
                self::FIELD_EMAIL                       => 
                    $strEmail,
                self::FIELD_WEBSITE                     => 
                    $strWebsite,
                self::FIELD_NOTES                       => 
                    $strNotes,
                self::FIELD_POSITION                    =>
                    $intPosition,
                self::FIELD_FILES                       => 
                    $arrModelFiles
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
            self::FIELD_COMPANY_REGISTRATION_NUMBER,
            self::FIELD_INCORPORATION_DATE,
            self::FIELD_SIC_CODES,
            self::FIELD_LEGAL_STATUS,
            self::FIELD_TRADING_ADDRESS_LINE_1,
            self::FIELD_TRADING_ADDRESS_LINE_2,
            self::FIELD_TRADING_ADDRESS_LINE_3,
            self::FIELD_TRADING_ADDRESS_LINE_4,
            self::FIELD_TRADING_ADDRESS_POSTCODE,
            self::FIELD_REGISTERED_ADDRESS_LINE_1,
            self::FIELD_REGISTERED_ADDRESS_LINE_2,
            self::FIELD_REGISTERED_ADDRESS_LINE_3,
            self::FIELD_REGISTERED_ADDRESS_LINE_4,
            self::FIELD_REGISTERED_ADDRESS_POSTCODE,
            self::FIELD_TELEPHONE,
            self::FIELD_EMAIL,
            self::FIELD_WEBSITE,
            self::FIELD_NOTES,
            self::FIELD_POSITION,
            self::FIELD_FILES
        ];
    }

    public function getName() : string
    {
        return $this->_getField(self::FIELD_NAME);
    }

    public function getCompanyRegistrationNumber() : string
    {
        return $this->_getField(self::FIELD_COMPANY_REGISTRATION_NUMBER);
    }

    public function getIncorporationDate() : DateTime
    {
        $datetimeIncorporationDate = DateTime::createFromFormat(
            self::INCORPORATION_DATE_FORMAT,
            $this->_getField(self::FIELD_INCORPORATION_DATE)
        );

        if ($datetimeIncorporationDate === false) {
            throw new Exception(
                "An error occured when retriving incorporation date - is " . 
                    print_r(
                        $this->_getField(self::FIELD_INCORPORATION_DATE), 
                        true
                    )
            );
        }

        return $datetimeIncorporationDate;
    }

    public function getSicCodes() : string
    {
        return $this->_getField(self::FIELD_SIC_CODES, "");
    }

    public function getLegalStatus() : int
    {
        return $this->_getField(
            self::FIELD_LEGAL_STATUS, 
            self::LEGAL_STATUS_LIMITED_COMPANY
        );
    }

    public function getTradingAddressLine1() : string
    {
        return $this->_getField(self::FIELD_TRADING_ADDRESS_LINE_1, "");
    }

    public function getTradingAddressLine2() : string
    {
        return $this->_getField(self::FIELD_TRADING_ADDRESS_LINE_2, "");
    }

    public function getTradingAddressLine3() : string
    {
        return $this->_getField(self::FIELD_TRADING_ADDRESS_LINE_3, "");
    }

    public function getTradingAddressLine4() : string
    {
        return $this->_getField(self::FIELD_TRADING_ADDRESS_LINE_4, "");
    }

    public function getTradingAddressPostcode() : string
    {
        return $this->_getField(self::FIELD_TRADING_ADDRESS_POSTCODE, "");
    }

    public function getRegisteredAddressLine1() : string
    {
        return $this->_getField(self::FIELD_REGISTERED_ADDRESS_LINE_1, "");
    }

    public function getRegisteredAddressLine2() : string
    {
        return $this->_getField(self::FIELD_REGISTERED_ADDRESS_LINE_2, "");
    }

    public function getRegisteredAddressLine3() : string
    {
        return $this->_getField(self::FIELD_REGISTERED_ADDRESS_LINE_3, "");
    }

    public function getRegisteredAddressLine4() : string
    {
        return $this->_getField(self::FIELD_REGISTERED_ADDRESS_LINE_4, "");
    }

    public function getRegisteredAddressPostcode() : string
    {
        return $this->_getField(self::FIELD_REGISTERED_ADDRESS_POSTCODE, "");
    }

    public function getTelephone() : string
    {
        return $this->_getField(self::FIELD_TELEPHONE, "");
    }

    public function getEmail() : string
    {
        return $this->_getField(self::FIELD_EMAIL, "");
    }

    public function getWebsite() : string
    {
        return $this->_getField(self::FIELD_WEBSITE, "");
    }

    public function getNotes() : string
    {
        return $this->_getField(self::FIELD_NOTES, "");
    }

    public function getPosition() : int
    {
        return $this->_getField(self::FIELD_POSITION, 0);
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
                File::COMPANY_CATEGORIES
            )
        ) {
            throw new Exception(
                "'" . $modelFile->getCategoryId() . "' " .
                    "is not a valid company file category"
            );
        }

        $arrModelFiles = $this->_getField(self::FIELD_FILES, []);

        $arrModelFiles[] = $modelFile;

        return $this->_setField(self::FIELD_FILES, $arrModelFiles);
    }
}