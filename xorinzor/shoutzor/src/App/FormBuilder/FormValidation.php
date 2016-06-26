<?php

namespace Xorinzor\Shoutzor\App\FormBuilder;

use Xorinzor\Shoutzor\App\FormBuilder\Fields\FormField;

class FormValidation {

    const TYPE_NUMERIC  = 'numeric';
    const TYPE_DIGIT    = 'digit';
    const TYPE_STRING   = 'string';
    const TYPE_BOOLEAN  = 'boolean';

    const REQ_NOTEMPTY  = 'isNotEmpty';
    const REQ_BETWEEN   = 'isBetween';
    const REQ_VALUE     = 'hasValue';

    private $hasErrors;

    public function __construct()
    {
        $this->hasErrors = false;
    }

    /**
     * Validates an input array, every input value should have it's own key
     * @param array
     * @return array
     */
    public function validateFields($fields)
    {
        $this->hasErrors = false;

        foreach($fields as $field)
        {
            $result = $this->validateField($field);
            if($result === false) {
                $this->hasErrors = true;
            }
        }
    }

    /**
     * Validates a single input
     * @param string key
     * @param mixed value
     * @param string type
     * @param bool resetErrorFlag
     * @return array
     */
    public function validateField(FormField $field)
    {
        switch($field->getValidationType())
        {
            case self::TYPE_NUMERIC:
                $validated = $this->isNumeric($field->getValue());
                $message = 'is not a numerical value';
                break;

            case self::TYPE_DIGIT:
                $validated = $this->isDigit($field->getValue());
                $message = 'must consist of characters 0-9 only';
                break;

            case self::TYPE_STRING:
                $validated = $this->isString($field->getValue());
                $message = 'is not a string';
                break;

            case self::TYPE_BOOLEAN:
                $validated = $this->isBoolean($field->getValue());
                $message = 'is not a boolean value (true or false)';
                break;

            default:
                $validated = true;
                break;
        }

        if($validated === false)
        {
            $field->setValidationError($message);
            return false;
        }
        else
        {
            $requirementCheck = $this->parseRequirementArray($field);
            if($requirementCheck !== true)
            {
                return $requirementCheck;
            }
        }

        return true;
    }

    /**
     * Parses an array of requirements
     * @param string name
     * @param string value
     * @param string type
     * @param array params
     * @param bool resetErrorFlag
     * @param false|array
     */
    public function parseRequirementArray(FormField $field)
    {
        foreach($field->getValidationRequirements() as $key=>$requirement)
        {

            if(is_array($requirement)) {
                $req_type   = $key;
                $params     = $requirement;
            } else {
                $req_type   = $requirement;
                $params     = array();
            }

            //Check every requirement, first one that fails gets returned
            $result = $this->parseRequirement($field, $req_type, $params);
            if($result === false)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Parses a value requirement
     * @param string name
     * @param string value
     * @param string type
     * @param array params
     * @param bool resetErrorFlag
     * @param false|array
     */
    private function parseRequirement(FormField $field, $type, $params = array())
    {
        switch($type)
        {
            case self::REQ_NOTEMPTY:
                $validated = $this->isNotEmpty($field->getValue());
                $message = 'cannot be empty';
                break;

            case self::REQ_BETWEEN:
                $validated = $this->isBetween($field->getValue(), $params['min'], $params['max']);
                $message = 'cannot be empty';
                break;

            case self::REQ_VALUE:
                $validated = $this->hasValue($field->getValue(), $params);
                $message = 'does not contain a valid value';
                break;

            default:
                $validated = false;
                $message = 'has an invalid requirement method provided';
                break;
        }

        if($validated === false)
        {
            $field->setValidationError($message);

            return false;
        }

        return true;
    }

    /**
     * Will show whether the previously executed validation threw errors
     * @return bool
     */
    public function hasErrors()
    {
        return $this->hasErrors;
    }

    /* type validation methods */

    public static function isNumeric($input)
    {
        return is_numeric($input) === true;
    }

    public static function isDigit($input)
    {
        return ctype_digit($input) === true;
    }

    public static function isString($input)
    {
        return is_string($input) === true;
    }

    public static function isBoolean($input)
    {
        return is_bool($input) === true;
    }

    /* type requirements */

    public static function isNotEmpty($input)
    {
        return is_null($input) === false && empty($input) === false;
    }

    public static function isBetween($input, $min, $max)
    {
        return ($input >= $min && $input <= $max);
    }

    public static function hasValue($input, $values) {
        return in_array($input, $values);
    }
}
