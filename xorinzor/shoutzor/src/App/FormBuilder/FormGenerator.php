<?php

namespace Xorinzor\Shoutzor\App\FormBuilder;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\FormField;

class FormGenerator {

    private $fields = array();
    private $fieldPointer = null;

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
        $formContent = '';

        foreach($this->fields as $field) {
            $formContent .= $field->render();
        }

        return $formContent;
    }
}
