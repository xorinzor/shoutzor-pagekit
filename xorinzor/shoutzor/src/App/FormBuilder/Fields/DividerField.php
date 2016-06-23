<?php


namespace Xorinzor\Shoutzor\App\FormBuilder\Fields;

use Xorinzor\Shoutzor\App\FormBuilder\Fields\FormField;

/**
 * Just a simple divider
 */
class DividerField extends FormField {

    public function __construct(){
        $this->id = "divider-".microtime();
    }

    public function render() {
        return '<hr />';
    }

}
