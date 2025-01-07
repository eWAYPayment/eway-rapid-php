<?php

declare(strict_types=1);

namespace Eway\Rapid\Service\Http;

/**
 * @codeCoverageIgnore
 */
class Curl
{
    /** @var false|resource $handler */
    private $handler;

    /**
     * @return void
     */
    public function init()
    {
        $this->handler = curl_init();
    }

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        curl_setopt_array($this->handler, $options);
    }

    /**
     * @return bool|string
     */
    public function execute()
    {
        return curl_exec($this->handler);
    }

    /**
     * @param int $option
     * @return mixed
     */
    public function getInfo(int $option)
    {
        return curl_getinfo($this->handler, $option);
    }

    /**
     * @return int
     */
    public function getErrorNo(): int
    {
        return curl_errno($this->handler);
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return curl_error($this->handler);
    }

    /**
     * @return void
     */
    public function close()
    {
        curl_close($this->handler);
    }

    /**
     * @return array|false
     */
    public function getVersion()
    {
        return curl_version();
    }
}
