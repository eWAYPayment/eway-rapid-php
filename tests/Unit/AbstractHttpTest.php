<?php

namespace Eway\Test\Unit;

use Eway\Test\AbstractTest;
use InterNations\Component\HttpMock\PHPUnit\HttpMockTrait;

abstract class AbstractHttpTest extends AbstractTest
{
    use HttpMockTrait;

    protected static $dns = 'localhost';
    protected static $port = '8082';

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::setUpHttpMockBeforeClass(self::$port, self::$dns);
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        static::tearDownHttpMockAfterClass();
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpHttpMock();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->tearDownHttpMock();
    }
}
