<?php

namespace Eway\Rapid\Enum;

/**
 * Class ApiMethod.
 */
abstract class ApiMethod extends AbstractEnum
{
    const DIRECT = 'Direct';
    const RESPONSIVE_SHARED = 'ResponsiveShared';
    const TRANSPARENT_REDIRECT = 'TransparentRedirect';
    const WALLET = 'Wallet';
    const AUTHORISATION = 'Authorisation';
}
