<?php

namespace Eway\Rapid\Enum;

/**
 * Defines the possible actions that may have been taken when/if an anti-fraud rule on the account has been triggered.
 */
abstract class FraudAction extends AbstractEnum
{
    const NOT_CHALLENGED = 0;
    const ALLOW = 1;
    const REVIEW = 2;
    const PRE_AUTH = 3;
    const PROCESSED = 4;
    const APPROVED = 5;
    const BLOCK = 6;
}
