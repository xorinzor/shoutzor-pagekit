<?php


namespace Xorinzor\Shoutzor\App\FormBuilder\Fields;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\FormField;

/**
 * Select field for the form generator
 * meant for <input /> with the types:
 * checkbox or radio (depending on the boolean value of parameter multiple)
 */
class CheckboxField extends FormField {

    private $options;
    private $multiple;

    public function __construct($id, $name, $title, $value = array(), $options = array(), $multiple = false, $description = '', $template = 'template.php')
    {
        $this->id = $id;
        $this->setName($name);
        $this->setTitle($title);
        $this->setValue($value);
        $this->setOptions($options);
        $this->setMultiple($multiple);
        $this->setDescription($description);
        $this->setTemplate($template);
    }

    public function getOptions() {
        return $this->options;
    }

    public function getMultiple() {
        return $this->multiple;
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

    public function render() {
        $type = ($this->getMultiple() === true) ? 'checkbox' : 'radio';
        $name = $this->getName() . (($this->getMultiple() === true) ? '[]]' : '');

        $content = '';
        $i = 0;
        foreach($this->getOptions() as $option) {
            $selected = (in_array($option['value'], $this->getValue())) ? 'checked' : '';
            $content .= '<input id="'.$this->getName().'-'.$i.'" type="'.$type.'" name="'. $name .'" value="'. $option['value'] .'" ' . $selected . '><label for="'.$this->getName().'-'.$i.'"> ' . $option['title'] . "</label>\n";
            $i++;
        }

        if(!empty($this->getDescription())) {
            $content .= ' <p class="uk-form-help-block">'. $this->getDescription() .'</p>';
        }

        $data = array(
            "%id%" => $this->getId(),
            "%title%" => $this->getTitle(),
            "%content%" => $content
        );

        return $this->parseTemplate($data);
    }

}
