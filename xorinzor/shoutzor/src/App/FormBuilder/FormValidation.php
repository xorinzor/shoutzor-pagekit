<?php

namespace Xorinzor\Shoutzor\App\FormBuilder;

require_once(__DIR__ . '/../Vendor/html2text/Html2Text.php');

use Pagekit\Application as App;
use Html2Text\Html2Text as Html2Text;

class FormValidation {

    const TYPE_NUMERIC  = 'numeric';
    const TYPE_DIGIT    = 'digit';
    const TYPE_STRING   = 'string';
    const TYPE_BOOLEAN  = 'boolean';

    const REQ_NOTEMPTY  = 'isNotEmpty';
    const REQ_BETWEEN   = 'isBetween';

    private $app;
    private $hasErrors;

    public function __construct(App $app)
    {
        $this->app = $app;
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
                $validated = $this->isNumeric($value);
                $message = 'is not a numerical value';
                break;

            case self::TYPE_DIGIT:
                $validated = $this->isDigit($value);
                $message = 'must consist of characters 0-9 only';
                break;

            case self::TYPE_STRING:
                $validated = $this->isString($value);
                $message = 'is not a string';
                break;

            case self::TYPE_BOOLEAN:
                $validated = $this->isBoolean($value);
                $message = 'is not a boolean value (true or false)';
                break;

            default:
                $validated = false;
                $message = 'has an invalid validation type provided';
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
    private function parseRequirement(FormField $field, $type, $params = array(), $resetErrorFlag = true)
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

    /**
     * Sanitizes the input string by removing all possible html
     * @param string input
     * @return string
     */
    public function sanitizeHTML($input)
    {
        if($this->isString($input) === true && $this->isNotEmpty($input) === true)
        {
            return Html2Text::convert($input);
        }
        else
        {
            return '';
        }
    }

    /* type validation methods */

    private function isNumeric($input)
    {
        return is_numeric($input) === true;
    }

    private function isDigit($input)
    {
        return ctype_digit($input) === true;
    }

    private function isString($input)
    {
        return is_string($input) === true;
    }

    private function isBoolean($input)
    {
        return is_bool($input) === true;
    }

    /* type requirements */

    private function isNotEmpty($input)
    {
        return is_null($input) === false && empty($input) === false;
    }

    private function isBetween($input, $min, $max)
    {
        return ($input >= $min && $input <= $max);
    }
}
