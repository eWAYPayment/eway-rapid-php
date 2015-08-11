<?php

namespace Eway\Rapid\Model\Response;

use Eway\Rapid\Model\AbstractModel;

/**
 * Class AbstractResponse.
 *
 * @property string $Errors A comma separated list of any error encountered, these can be looked up in the Response
 *     Codes section.
 */
abstract class AbstractResponse extends AbstractModel
{
    protected $errors = [];

    /**
     * @return array
     */
    public function getErrors()
    {
        $errors = array_key_exists('Errors', $this->attributes) ? $this->attributes['Errors'] : '';
        if (!is_string($errors) || strlen(trim($errors)) === 0) {
            $errors = [];
        } else {
            $errors = explode(',', $errors);
        }

        return array_merge($this->errors, $errors);
    }

    /**
     * @param $errorCode
     *
     * @return $this
     */
    public function addError($errorCode)
    {
        $this->errors[] = $errorCode;
        if (!array_key_exists('Errors', $this->attributes)) {
            $this->attributes['Errors'] = '';
        }

        return $this;
    }
}
