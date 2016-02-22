<?php

namespace Eway\Rapid\Enum;

/**
 * Class LogLevel.
 *
 * PSR-3 log levels
 */
abstract class LogLevel extends AbstractEnum
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
}
