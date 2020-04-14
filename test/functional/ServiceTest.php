<?php

namespace jlgtechnology;

use PHPUnit\Framework\TestCase;

use jlgtechnology\model\{
    File as ModelFile
};

use \Exception as Exception;

class ServiceTest extends TestCase
{
    // private $_strClientId = "JJnSJT0ibfYn6yiiwM6Boy0hnes81sPR";
    // private $_strClientSecret = 
    //     "gBGRAW-2WXT3nNepwxLSjEVuCsf22Qhn4YZUWHJYwFy7_fH20f8JCiHqJrSgrXBG";
    private $_strClientId = "OQfWZVMzNIq393epCSERsMYPA14F9B2m";
    private $_strClientSecret = 
        "i-WWCJmva-bX19SRYw3w7qS0GZdz_p2CqhcIiGVgTn4mDZt7CLqjXLF3qvW476df";

    public function testCreateFromCredentials_Invalid_Credentials()
    {
        $strClientId = "abc";
        $strClientSecret = "def";

        $this->expectException(Exception::class);
        $this->expectExceptionCode(401);

        $objService = Service::createFromCredentials(
            $strClientId,
            $strClientSecret
        );
    }

    public function testUploadFile()
    {
        $objService = Service::createFromCredentials(
            $this->_strClientId,
            $this->_strClientSecret
        );

        $modelFile = ModelFile::create(
            "test.txt",
            "text/plain",
            "Test",
            ModelFile::CATEGORY_SEARCHES
        );

        $arrResponse = $objService->_uploadFile($modelFile);

        exit(var_dump($arrResponse));
    }
}