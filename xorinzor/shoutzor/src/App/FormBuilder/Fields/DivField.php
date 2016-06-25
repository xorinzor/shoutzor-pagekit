<?php


namespace Xorinzor\Shoutzor\App\FormBuilder\Fields;

use Xorinzor\Shoutzor\App\FormBuilder\Fields\FormField;

/**
 * Div field for the form generator
 */
class DivField extends FormField {

    public function __construct($title, $value = '', $description = '', $classes = '', $attributes = '', $template = 'template.php')
    {
        $this->id = "div-".microtime();
        $this->setTitle($title);
        $this->setValue($value);
        $this->setDescription($description);
        $this->setClasses($classes);
        $this->setAttributes($attributes);
        $this->setTemplate($template);
    }

    public function render() {

        $content = '<div class="'. $this->getClasses() .'" '. $this->getAttributes() .'>'. $this->getValue() .'</div>';

        if(!empty($this->getDescription())) {
            $content .= ' <p class="uk-form-help-block">'. $this->getDescription() .'</p>';
        }

        if(!empty($this->getValidationError())) {
            $content .= ' <p class="uk-text-danger">'. $this->getValidationError() .'</p>';
        }

        if(!empty($this->getValidationSuccess())) {
            $content .= ' <p class="uk-text-success">'. $this->getValidationSuccess() .'</p>';
        }

        $data = array(
            "%id%" => $this->getId(),
            "%title%" => $this->getTitle(),
            "%content%" => $content
        );

        return $this->parseTemplate($data);
    }

}
