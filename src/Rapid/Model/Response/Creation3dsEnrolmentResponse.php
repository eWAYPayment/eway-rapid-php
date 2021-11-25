<?php

namespace Eway\Rapid\Model\Response;

/**
 * @property string Errors
 * @property string Default3dsUrl
 * @property string AccessCode
 */
class Creation3dsEnrolmentResponse extends AbstractResponse
{
    protected $fillable = [
        'Errors',
        'Default3dsUrl',
        'AccessCode',
    ];
}
