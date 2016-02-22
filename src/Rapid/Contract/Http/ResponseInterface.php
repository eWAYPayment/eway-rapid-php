<?php

namespace Eway\Rapid\Contract\Http;

/**
 * Interface ResponseInterface.
 */
interface ResponseInterface
{
    /**
     * Gets the body of the message.
     *
     * @return string
     */
    public function getBody();

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode();

    /**
     * Gets the error message if one occurred
     *
     * @return string
     */
    public function getError();
}
