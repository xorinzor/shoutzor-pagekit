<?php

namespace Xorinzor\Shoutzor\App\FormBuilder;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\FormField;

class FormGenerator {

    private $app;
    private $fields = array();
    private $fieldPointer = null;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Sets the pointer to the desired field
     */
    public function selectField($id) {
        $this->fieldPointer = $id;
    }

    /**
     * Creates a new field and sets the pointer to this field
     */
    public function addField($id, $name, $type, $title, $value = '', $description = '') {
        $this->fields[$id] = new FormField($id, $name, $type, $title, $value, $description);
        $this->fieldPointer = $id;
    }

    /**
     * Sets the type of the currently selected field
     */
    public function setFieldName($name) {
        $this->fields[$this->fieldPointer]->setName($name);
    }

    /**
     * Sets the type of the currently selected field
     */
    public function setFieldType($type) {
        $this->fields[$this->fieldPointer]->setType($type);
    }

    /**
     * Sets the title of the currently selected field
     */
    public function setFieldTitle($title) {
        $this->fields[$this->fieldPointer]->setTitle($title);
    }

    /**
     * Sets the value of the currently selected field
     */
    public function setFieldValue($value) {
        $this->fields[$this->fieldPointer]->setValue($value);
    }

    /**
     * Sets the description of the currently selected field
     */
    public function setFieldDescription($description) {
        $this->fields[$this->fieldPointer]->setDescription($description);
    }

    private function buildField(FormField $field) {
        $result = '<div class="uk-form-row">';
        $result .= '<label class="uk-form-label">' . $field->getTitle() . '</label>';
        $result .= '<div class="uk-form-controls">';
        $result .= '<input type="' . $field->getType() . '" class="uk-form-width-large" value="' . $field->getValue() . '">';

        if(!empty($field->getDescription())) {
            $result .= '<span class="uk-form-help-inline">' . $field->getDescription() . '</span>';
        }

        $result .= '</div></div>';

        return $result;
    }

    public function render() {
        $formContent = '';

        foreach($this->fields as $field) {
            $formContent .= $this->buildField($field);
        }

        return $formContent;
    }
}
