<?php
declare(strict_types=1);

namespace jlg_technology\model;

use \Exception as Exception;

class File extends AbstractModel
{
    const FIELD_NAME        = "Name";
    const FIELD_MIME_TYPE   = "MimeType";
    const FIELD_DESCRIPTION = "Description";
    const FIELD_CATEGORY_ID = "CategoryId";
    const FIELD_UPLOAD_PATH = "UploadPath";

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
    public function getName() : string
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