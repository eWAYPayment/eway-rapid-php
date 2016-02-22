<?php

namespace Eway\Rapid\Service\Http;

use Eway\Rapid\Contract\Http\ResponseInterface;
use Eway\Rapid\Model\Support\CanGetClassTrait;

/**
 * Class Response.
 */
class Response implements ResponseInterface
{
    use CanGetClassTrait;

    /** @var int */
    private $statusCode = 200;

    /**
     * @param int    $status Status code for the response, if any.
     * @param string $body   Response body.
     */
    public function __construct($status = 200, $body = null, $error = null)
    {
        $this->statusCode = (int)$status;
        $this->body = $body;
        $this->error = $error;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getError()
    {
        return $this->error;
    }
}
