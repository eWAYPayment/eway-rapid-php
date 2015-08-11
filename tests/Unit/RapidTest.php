<?php

namespace Eway\Test\Unit;

use Eway\Rapid;
use Eway\Test\AbstractTest;

/**
 * Class RapidTest
 */
class RapidTest extends AbstractTest
{
    /**
     * Create client via factory method
     */
    public function testCreateClient()
    {
        $client = Rapid::createClient('', '');
        $this->assertInstanceOf('Eway\Rapid\Contract\Client', $client);
    }

    /**
     * Get error message with valid error code
     *
     * @dataProvider errorCodeProvider
     *
     * @param $code
     * @param $message
     */
    public function testGetMessageReturnValidErrorMessage($code, $message)
    {
        $this->assertEquals($message, Rapid::getMessage($code));
    }

    /**
     * Get error message with invalid error code
     *
     * @dataProvider invalidErrorCodeProvider
     *
     * @param $code
     */
    public function testGetMessageReturnInvalidErrorMessage($code)
    {
        $this->assertEquals($code, Rapid::getMessage($code));
    }

    /**
     * Get error message with valid error code and locale other than en
     *
     * @dataProvider errorCodeProvider
     *
     * @param $code
     * @param $message
     */
    public function testGetMessageReturnDefaultEnglishLanguage($code, $message)
    {
        $this->assertEquals($message, Rapid::getMessage($code, 'vn'));
    }

    /**
     * @return array
     */
    public function errorCodeProvider()
    {
        return [
            ['A2000', 'Transaction Approved'],
            ['D4401', 'Refer to Issuer'],
            ['F7000', 'Undefined Fraud Error'],
            ['S5000', 'System Error'],
            ['V6000', 'Validation error'],
        ];
    }

    /**
     * @return array
     */
    public function invalidErrorCodeProvider()
    {
        return [
            [null],
            [''],
            ['foo'],
        ];
    }
}
