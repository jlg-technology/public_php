<?php
declare(strict_types=1);

namespace jlgtechnology\model;

use \DateTime as DateTime;

use \Exception as Exception;

class Person extends AbstractModel
{
    const FIELD_FORENAME           = "Forename";
    const FIELD_MIDDLENAME         = "MiddleName";
    const FIELD_SURNAME            = "Surname";
    const FIELD_DOB                = "DoB";
    const FIELD_GENDER             = "Gender";
    const FIELD_TITLE              = "Title";
    const FIELD_ADDRESS_LINE_1     = "Address1";
    const FIELD_ADDRESS_LINE_2     = "Address2";
    const FIELD_ADDRESS_LINE_3     = "Address3";
    const FIELD_ADDRESS_LINE_4     = "Address4";
    const FIELD_ADDRESS_POSTCODE   = "AddressPostcode";
    const FIELD_DAY_PHONE          = "DayPhone";
    const FIELD_MOBILE_PHONE       = "MobilePhone";
    const FIELD_EMAIL              = "Email";
    const FIELD_NOTES              = "Notes";
    const FIELD_POSITION           = "Position";
    const FIELD_IS_PRIMARY_CONTACT = "PrimaryContact";
    const FIELD_FILES              = "Files";

    const TITLE_MR   = "Mr";
    const TITLE_MRS  = "Mrs";
    const TITLE_MISS = "Miss";
    const TITLE_MS   = "Ms";
    const TITLE_DR   = "Dr";

    const POSITION_DIRECTOR_BIT   = 1;
    const POSITION_GUARANTOR_BIT  = 2;
    const POSITION_PSC_BIT        = 4;
    const POSITION_NO_CONTACT_BIT = 8;

    const GENDER_MALE   = 0;
    const GENDER_FEMALE = 1;

    const DATE_OF_BIRTH_FORMAT = "Y-m-d H:i:s";

    const POSTCODE_REGEX = 
        "/^\s*((([A-Z]{1,2}[0-9][A-Z0-9]?" .
        "|ASCN|STHL|TDCU|BBND|[BFS]IQQ|PCRN|TKCA) ?[0-9][A-Z]{2}|BFPO ?" .
        "[0-9]{1,4}|(KY[0-9]|MSR|VG|AI)[ -]?[0-9]{4}|[A-Z]{2} ?[0-9]{2}|GE ?" .
        "CX|GIR ?0A{2}|SAN ?TA1))\s*$/i";

    public static function create(
        string $strForename,
        ?string $strMiddleName,
        string $strSurname,
        DateTime $datetimeDateOfBirth,
        int $intGender,
        ?string $strTitle,
        ?string $strAddressOne,
        ?string $strAddressTwo,
        ?string $strAddressThree,
        ?string $strAddressFour,
        ?string $strAddressPostcode,
        ?string $strDayPhone,
        ?string $strMobilePhone,
        ?string $strEmail,
        ?string $strNotes,
        ?int $intPosition,
        ?bool $boolPrimaryContact,
        ?array $arrModelFiles
    ) : self
    {
        if (
            !in_array(
                $intGender,
                [
                    self::GENDER_FEMALE,
                    self::GENDER_MALE
                ]
            )
        ) {
            throw new Exception("'$intGender' is not a valid gender");
        }

        if (
            !is_null($strTitle) &&
            !in_array(
                $strTitle,
                [
                    self::TITLE_MR,
                    self::TITLE_MRS,
                    self::TITLE_MISS,
                    self::TITLE_MS,
                    self::TITLE_DR
                ]
            )
        ) {
            throw new Exception("'$strTitle' is not a valid title");
        }

        if (
            !is_null($strAddressPostcode) &&
            !preg_match(self::POSTCODE_REGEX, $strAddressPostcode)
        ) {
            throw new Exception(
                "'$strAddressPostcode' is not a valid UK postcode"
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
                        File::PERSON_CATEGORIES
                    )
                ) {
                    throw new Exception(
                        "'" . $modelFile->getCategoryId() . "' " . 
                            "on file '" . $modelFile->getNameAndPath() . "' " .
                            "is not a valid person category"
                    );
                }
            }
        }

        return new self(
            [
                self::FIELD_FORENAME           => $strForename,
                self::FIELD_MIDDLENAME         => $strMiddleName,
                self::FIELD_SURNAME            => $strSurname,
                self::FIELD_DOB                => $datetimeDateOfBirth
                    ->format(self::DATE_OF_BIRTH_FORMAT),
                self::FIELD_GENDER             => $intGender,
                self::FIELD_TITLE              => $strTitle,
                self::FIELD_ADDRESS_LINE_1     => $strAddressOne,
                self::FIELD_ADDRESS_LINE_2     => $strAddressTwo,
                self::FIELD_ADDRESS_LINE_3     => $strAddressThree,
                self::FIELD_ADDRESS_LINE_4     => $strAddressFour,
                self::FIELD_ADDRESS_POSTCODE   => $strAddressPostcode,
                self::FIELD_DAY_PHONE          => $strDayPhone,
                self::FIELD_MOBILE_PHONE       => $strMobilePhone,
                self::FIELD_EMAIL              => $strEmail,
                self::FIELD_NOTES              => $strNotes,
                self::FIELD_POSITION           => $intPosition,
                self::FIELD_IS_PRIMARY_CONTACT => $boolPrimaryContact,
                self::FIELD_FILES              => $arrModelFiles
            ]
        );
    }

    /**
     * Getters
     */
    public static function getFields() : array
    {
        return [
            self::FIELD_FORENAME,
            self::FIELD_MIDDLENAME,
            self::FIELD_SURNAME,
            self::FIELD_DOB,
            self::FIELD_GENDER,
            self::FIELD_TITLE,
            self::FIELD_ADDRESS_LINE_1,
            self::FIELD_ADDRESS_LINE_2,
            self::FIELD_ADDRESS_LINE_3,
            self::FIELD_ADDRESS_LINE_4,
            self::FIELD_ADDRESS_POSTCODE,
            self::FIELD_DAY_PHONE,
            self::FIELD_MOBILE_PHONE,
            self::FIELD_EMAIL,
            self::FIELD_NOTES,
            self::FIELD_POSITION,
            self::FIELD_IS_PRIMARY_CONTACT,
            self::FIELD_FILES
        ];
    }

    public function getForename() : string
    {
        return $this->_getField(self::FIELD_FORENAME);
    }

    public function getMiddleName() : string
    {
        return $this->_getField(self::FIELD_MIDDLENAME, "");
    }

    public function getSurname() : string
    {
        return $this->_getField(self::FIELD_SURNAME);
    }

    public function getDateOfBirth() : DateTime
    {
        $datetimeIncorporationDate = DateTime::createFromFormat(
            self::DATE_OF_BIRTH_FORMAT,
            $this->_getField(self::FIELD_DOB)
        );

        if ($datetimeIncorporationDate === false) {
            throw new Exception(
                "An error occured when retriving date of birth - is " . 
                    print_r($this->_getField(self::FIELD_DOB), true)
            );
        }

        return $datetimeIncorporationDate;
    }

    public function getGender() : int
    {
        return $this->_getField(self::FIELD_GENDER);
    }

    public function getTitle() : string
    {
        return $this->_getField(self::FIELD_TITLE, "");
    }

    public function getAddressLine1() : string
    {
        return $this->_getField(self::FIELD_ADDRESS_LINE_1, "");
    }

    public function getAddressLine2() : string
    {
        return $this->_getField(self::FIELD_ADDRESS_LINE_2, "");
    }

    public function getAddressLine3() : string
    {
        return $this->_getField(self::FIELD_ADDRESS_LINE_3, "");
    }

    public function getAddressLine4() : string
    {
        return $this->_getField(self::FIELD_ADDRESS_LINE_4, "");
    }

    public function getAddressPostcode() : string
    {
        return $this->_getField(self::FIELD_ADDRESS_POSTCODE, "");
    }

    public function getDayPhone() : string
    {
        return $this->_getField(self::FIELD_DAY_PHONE, "");
    }

    public function getMobilePhone() : string
    {
        return $this->_getField(self::FIELD_MOBILE_PHONE, "");
    }

    public function getEmail() : string
    {
        return $this->_getField(self::FIELD_EMAIL, "");
    }

    public function getNotes() : string
    {
        return $this->_getField(self::FIELD_NOTES, "");
    }

    public function getPosition() : int
    {
        return $this->_getField(self::FIELD_POSITION, 0);
    }

    public function getIsPrimaryContact() : bool
    {
        return $this->_getField(self::FIELD_IS_PRIMARY_CONTACT, false);
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
                File::PERSON_CATEGORIES
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