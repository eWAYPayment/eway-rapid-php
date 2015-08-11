<?php

namespace Eway\Test;

use Prophecy\Prophet;

/**
 * Class AbstractTest.
 */
abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Prophet
     */
    protected $prophet;

    /**
     *
     */
    protected function setUp()
    {
        $this->prophet = new Prophet();
    }

    /**
     * This method is called after a test is executed.
     * Checks Prophet's predictions
     */
    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }
}
