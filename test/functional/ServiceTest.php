<?php

namespace jlgtechnology;

use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    public function testCreateFromCredentials()
    {
        $strClientId = "abc";
        $strClientSecret = 
            "def";

        $objService = Service::createFromCredentials(
            $strClientId,
            $strClientSecret
        );
    }
}