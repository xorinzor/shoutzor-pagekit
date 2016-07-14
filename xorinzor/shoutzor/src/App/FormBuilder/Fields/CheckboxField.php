<?php


namespace Xorinzor\Shoutzor\App\FormBuilder\Fields;

use Xorinzor\Shoutzor\App\FormBuilder\Fields\FormField;

/**
 * Select field for the form generator
 * meant for <input /> with the types:
 * checkbox or radio (depending on the boolean value of parameter multiple)
 */
class CheckboxField extends FormField {

    private $options;
    private $multiple;

    public function __construct($id, $name, $title, $value = array(), $options = array(), $multiple = false, $description = '', $classes = '', $attributes = '', $template = 'template.php')
    {
        $this->id = $id;
        $this->setName($name);
        $this->setTitle($title);
        $this->setValue($value);
        $this->setOptions($options);
        $this->setMultiple($multiple);
        $this->setDescription($description);
        $this->setClasses($classes);
        $this->setAttributes($attributes);
        $this->setTemplate($template);
    }

    public function getOptions() {
        return $this->options;
    }

    public function getMultiple() {
        return $this->multiple;
    }

    public function getValue() {
        if($this->getMultiple() === false) {
            return $this->value[0];
        }

        return $this->value;
    }

    public function setValue($value) {
        if(!is_array($value)) {
            $value = array($value);
        }

        $this->value = $value;
    }

    public function setOptions($options) {
        $this->options = $options;
    }

    public function setMultiple($multiple) {
        $this->multiple = $multiple;
    }

    private function isSelected($value) {
        if($this->getMultiple()) {
            return in_array($value, $this->getValue());
        } else {
            return $value == $this->getValue();
        }
    }

    public function render() {
        $type = ($this->getMultiple() === true) ? 'checkbox' : 'radio';
        $name = $this->getName() . (($this->getMultiple() === true) ? '[]]' : '');

        $content = '';
        $i = 0;
        foreach($this->getOptions() as $option) {
            $selected = ($this->isSelected($option['value'])) ? 'checked' : '';
            $content .= '<input class="'. $this->getClasses() .'" id="'.$this->getName().'-'.$i.'" type="'.$type.'" name="'. $name .'" value="'. $option['value'] .'" ' . $selected . ' '. $this->getAttributes() .' ><label for="'.$this->getName().'-'.$i.'"> ' . $option['title'] . "</label>\n";
            $i++;
        }

        if(!empty($this->getDescription())) {
            $content .= ' <p class="uk-form-help-block">'. $this->getDescription() .'</p>';
        }

        if(!empty($this->getValidationError())) {
            $content .= ' <p class="uk-text-danger validationMessage">'. $this->getValidationError() .'</p>';
        }

        if(!empty($this->getValidationSuccess())) {
            $content .= ' <p class="uk-text-success validationMessage">'. $this->getValidationSuccess() .'</p>';
        }

        $data = array(
            "%id%" => $this->getId(),
            "%title%" => $this->getTitle(),
            "%content%" => $content
        );

        return $this->parseTemplate($data);
    }

}
