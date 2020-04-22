<?php

namespace jlgtechnology\model;

use PHPUnit\Framework\TestCase;

use \Exception as Exception;

/**
 * These functions need to be mocked as they're used to validate the model
 * data
 */
function file_exists(string $strFileName) : bool
{
    if (strpos($strFileName, 'fake') !== false) {
        return false;
    }

    return true;
}

function is_file(string $strFileName) : bool
{
    if (strpos($strFileName, 'directory') !== false) {
        return false;
    }

    return true;
}

function is_readable(string $strFileName) : bool
{
    if (strpos($strFileName, 'unreadable') !== false) {
        return false;
    }

    return true;
}

class FileTest extends TestCase
{
    public function testGetFields()
    {
        $this->assertEquals(
            [
                "NameAndPath",
                "MimeType",
                "Description",
                "CategoryId",
                "UploadPath"
            ],
            File::getFields()
        );
    }

    public function testCreate()
    {
        $strName = "Test 1";
        $strMimeType = "Test 2";
        $strDescription = "Test 3";
        $strCategoryId = "Other";

        $modelFile = File::create(
            $strName,
            $strMimeType,
            $strDescription,
            $strCategoryId
        );

        $this->assertEquals(
            $strName,
            $modelFile->getNameAndPath()
        );

        $this->assertEquals(
            $strMimeType,
            $modelFile->getMimeType()
        );

        $this->assertEquals(
            $strDescription,
            $modelFile->getDescription()
        );

        $this->assertEquals(
            $strCategoryId,
            $modelFile->getCategoryId()
        );

        $this->assertEquals(
            null,
            $modelFile->getUploadPath()
        );
    }

    public function testCreate_File_Doesnt_Exist()
    {
        $strName = "fake";
        $strMimeType = "Test 2";
        $strDescription = "Test 3";
        $strCategoryId = "Other";

        $this->expectException(Exception::class);

        $modelFile = File::create(
            $strName,
            $strMimeType,
            $strDescription,
            $strCategoryId
        );
    }

    public function testCreate_File_Not_File()
    {
        $strName = "directory";
        $strMimeType = "Test 2";
        $strDescription = "Test 3";
        $strCategoryId = "Other";

        $this->expectException(Exception::class);

        $modelFile = File::create(
            $strName,
            $strMimeType,
            $strDescription,
            $strCategoryId
        );
    }

    public function testCreate_File_Unreadable()
    {
        $strName = "unreadable";
        $strMimeType = "Test 2";
        $strDescription = "Test 3";
        $strCategoryId = "Other";

        $this->expectException(Exception::class);

        $modelFile = File::create(
            $strName,
            $strMimeType,
            $strDescription,
            $strCategoryId
        );
    }

    public function testCreate_Category_Invalid()
    {
        $strName = "Test 1";
        $strMimeType = "Test 2";
        $strDescription = "Test 3";
        $strCategoryId = "Invalid";

        $this->expectException(Exception::class);

        $modelFile = File::create(
            $strName,
            $strMimeType,
            $strDescription,
            $strCategoryId
        );
    }

    public function testSetUploadPath()
    {
        $strName = "Test 1";
        $strMimeType = "Test 2";
        $strDescription = "Test 3";
        $strCategoryId = "Other";
        $strUploadPath = "Test 4";

        $modelFile = File::create(
            $strName,
            $strMimeType,
            $strDescription,
            $strCategoryId
        );

        $this->assertEquals(
            null,
            $modelFile->getUploadPath()
        );

        $modelFile->setUploadPath($strUploadPath);

        $this->assertEquals(
            $strUploadPath,
            $modelFile->getUploadPath()
        );
    }
}