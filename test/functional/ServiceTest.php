<?php

namespace jlgtechnology;

use PHPUnit\Framework\TestCase;

use \Exception as Exception;

class ServiceTest extends TestCase
{
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