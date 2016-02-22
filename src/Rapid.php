<?php

namespace Eway;

use Eway\Rapid\Client;
use Eway\Rapid\Contract\Client as ClientContract;

/**
 * eWAY Rapid
 *
 * Provides static access to create an eWAY Rapid Client and fetch error message
 * transalations.
 *
 * Example:
 *
 * <code>
 * $apiKey = 'YOUR-API-KEY';
 * $apiPassword = 'YOUR-API-PASSWORD';
 * $apiEndpoint = 'Sandbox';
 * $client = \Eway\Rapid::createClient($apiKey, $apiPassword, $apiEndpoint);
 * </code>
 *
 */
abstract class Rapid
{
    /**
     * Cached messages.
     *
     * @var array
     */
    private static $messages = null;

    /**
     * Static method to create a new Rapid Client object configured to communicate with a specific instance of the
     * Rapid API.
     *
     * @param string $apiKey eWAY Rapid API key
     * @param string $apiPassword eWAY Rapid API password
     * @param string $endpoint  eWAY Rapid API endpoint - one of 'Sandbox' or 'Production'
     * @param Psr\Log\LoggerInterface $logger PSR-3 logger
     *
     * @return ClientContract an eWAY Rapid Client
     */
    public static function createClient($apiKey, $apiPassword, $endpoint = ClientContract::MODE_SANDBOX, $logger = null)
    {
        return new Client($apiKey, $apiPassword, $endpoint, $logger);
    }

    /**
     * This static method provides a message suitable for display to a user corresponding to a given Rapid
     * Code & language.
     *
     * @param string $errorCode
     * @param string $language 2 character language code, defaults to en
     *
     * @return string
     */
    public static function getMessage($errorCode, $language = 'en')
    {
        self::initMessages();

        $messagesByLanguage = self::getMessagesByLanguage($language);
        if (!array_key_exists($errorCode, $messagesByLanguage)) {
            return $errorCode;
        }

        return $messagesByLanguage[$errorCode];
    }

    /**
     * @param string $language
     */
    private static function tryLoadingMessageFile($language)
    {
        $language = strtolower($language);
        $file = __DIR__.'/../resource/lang/'.$language.'.ini';
        if (file_exists($file)) {
            self::$messages[$language] = parse_ini_file($file);
        }
    }

    /**
     */
    private static function initMessages()
    {
        if (null === self::$messages) {
            self::$messages = [];
        }
    }

    /**
     * @param string $language
     *
     * @return array
     */
    private static function getMessagesByLanguage($language)
    {
        $messages = [];

        if (!array_key_exists($language, self::$messages)) {
            self::tryLoadingMessageFile($language);
        }

        if (array_key_exists($language, self::$messages)) {
            $messages = self::$messages[$language];
        } else {
            self::tryLoadingMessageFile('en');
            if (array_key_exists('en', self::$messages)) {
                $messages = self::$messages['en'];
            }
        }

        return $messages;
    }
}
