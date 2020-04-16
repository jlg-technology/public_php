<?php

namespace jlgtechnology;

use PHPUnit\Framework\TestCase;

use jlgtechnology\model\{
    File as ModelFile,
    Company as ModelCompany,
    Person as ModelPerson,
    Loan as ModelLoan
};

use \DateTime as DateTime;

use \Exception as Exception;

class ServiceTest extends TestCase
{
    protected function setUp()
    {
        Service::setDebugMode(true);
    }

    protected function tearDown()
    {
        Service::setDebugMode(false);
    }

    public function testCreateFromToken()
    {
        $strJWT = "Test";

        $objService = Service::createFromToken($strJWT);

        $this->assertEquals(
            $strJWT,
            $objService->getToken()
        );
    }

    public function testCreateFromCredentials_Invalid_Credentials()
    {
        $strClientId = "abc";
        $strClientSecret = "def";

        $this->expectException(Exception::class);
        $this->expectExceptionCode(401);

        Service::createFromCredentials(
            $strClientId,
            $strClientSecret
        );
    }
}