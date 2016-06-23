<?php

namespace Xorinzor\Shoutzor\App\FormBuilder;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\FormField;

class FormGenerator {

    private $fields = array();
    private $fieldPointer = null;

    private $target;
    private $method;
    private $classes;
    private $id;

    public function __construct($target, $method, $classes = '', $id = '') {
        $this->target = $target;
        $this->method = $method;
        $this->classes = $classes;
        $this->id = $id;
    }

    /**
     * Sets the pointer to the desired field
     */
    public function selectField($id) {
        $this->fieldPointer = $this->fields[$id];
    }

    /**
     * Creates a new field and sets the pointer to this field
     */
    public function addField(FormField $field) {
        $this->fields[$field->getId()] = $field;
        $this->fieldPointer = $field;
    }

    /**
     * Sets the type of the currently selected field
     */
    public function setFieldName($name) {
        $this->fieldPointer->setName($name);
    }

    /**
     * Sets the type of the currently selected field
     */
    public function setFieldType($type) {
        $this->fieldPointer->setType($type);
    }

    /**
     * Sets the title of the currently selected field
     */
    public function setFieldTitle($title) {
        $this->fieldPointer->setTitle($title);
    }

    /**
     * Sets the value of the currently selected field
     */
    public function setFieldValue($value) {
        $this->fieldPointer->setValue($value);
    }

    /**
     * Sets the description of the currently selected field
     */
    public function setFieldDescription($description) {
        $this->fieldPointer->setDescription($description);
    }

    public function render() {
        $formContent = '<form id="'. $this->id .'" class="'. $this->classes .'" action="'. $this->target .'" method="' . $this->method . '">';

        foreach($this->fields as $field) {
            $formContent .= $field->render();
        }

        $formContent .= '</form>';

        return $formContent;
    }
}
