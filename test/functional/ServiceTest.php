<?php

namespace jlgtechnology;

use PHPUnit\Framework\TestCase;

use jlgtechnology\model\{
    File as ModelFile
};

use \Exception as Exception;

class ServiceTest extends TestCase
{
    private $_strClientId = "JJnSJT0ibfYn6yiiwM6Boy0hnes81sPR";
    private $_strClientSecret = 
        "gBGRAW-2WXT3nNepwxLSjEVuCsf22Qhn4YZUWHJYwFy7_fH20f8JCiHqJrSgrXBG";

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
}