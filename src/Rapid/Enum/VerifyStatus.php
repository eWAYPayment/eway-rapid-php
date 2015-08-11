<?php

namespace Eway\Rapid\Enum;

/**
 * Possible values returned from the payment providers with regards to verification of card/user details
 */
abstract class VerifyStatus extends AbstractEnum
{
    const UNCHECKED = 0;
    const VALID = 1;
    const INVALID = 2;
}
