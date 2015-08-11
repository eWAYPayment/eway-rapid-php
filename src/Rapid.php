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
 * $apiEndpoint = \Eway\Rapid\Contract::MODE_SANDBOX;
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
     * Static method to create a new Rapid SDK Client object configured to communicate with a specific instance of the
     * Rapid API. In some languages it may be appropriate to use a constructor with parameters instead of a static
     * method.
     *
     * @param string $apiKey eWAY Rapid API key
     * @param string $apiPassword eWAY Rapid API password
     * @param string $endpoint  eWAY Rapid API endpoint
     *
     * @return ClientContract an eWAY Rapid Client
     */
    public static function createClient($apiKey, $apiPassword, $endpoint = ClientContract::MODE_SANDBOX)
    {
        return new Client($apiKey, $apiPassword, $endpoint);
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
        self::_initMessages();

        $messagesByLanguage = self::_getMessagesByLanguage($language);
        if (!array_key_exists($errorCode, $messagesByLanguage)) {
            return $errorCode;
        }

        return $messagesByLanguage[$errorCode];
    }

    /**
     * @param string $language
     */
    private static function _tryLoadingMessageFile($language)
    {
        $language = strtolower($language);
        $file = __DIR__.'/../resource/lang/'.$language.'.ini';
        if (file_exists($file)) {
            self::$messages[$language] = parse_ini_file($file);
        }
    }

    /**
     */
    private static function _initMessages()
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
    private static function _getMessagesByLanguage($language)
    {
        $messages = [];

        if (!array_key_exists($language, self::$messages)) {
            self::_tryLoadingMessageFile($language);
        }

        if (array_key_exists($language, self::$messages)) {
            $messages = self::$messages[$language];
        } else {
            self::_tryLoadingMessageFile('en');
            if (array_key_exists('en', self::$messages)) {
                $messages = self::$messages['en'];
            }
        }

        return $messages;
    }
}
