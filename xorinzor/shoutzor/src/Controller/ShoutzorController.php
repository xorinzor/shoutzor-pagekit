<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\FormBuilder\FormGenerator;
use Xorinzor\Shoutzor\App\FormBuilder\FormValidation;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\InputField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\SelectField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\DividerField;

/**
 * @Access(admin=true)
 */
class ShoutzorController
{
    public function indexAction()
    {

        $request = App::request();

        $baseConfig = App::module('shoutzor')->config('shoutzor');
        $config = App::config('shoutzor')->toArray();
        $config = array_merge($baseConfig, $config);
        $config = array_merge($config, $_POST);

        $form = new FormGenerator('', 'POST', 'uk-form uk-form-horizontal');

        $form->addField(new SelectField(
            "upload",
            "upload",
            "Allow Uploads",
            "text",
            $config['upload'],
            array(['value' => 0, 'title' => 'Disabled'], ['value' => 1, 'title' => 'Enabled']),
            false,
            "Changing this setting will not delete uploaded content")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new SelectField(
            "request",
            "request",
            "Allow Requests",
            "text",
            $config['request'],
            array(['value' => 0, 'title' => 'Disabled'], ['value' => 1, 'title' => 'Enabled']),
            false,
            "Changing this setting will allow / deny user requests")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new DividerField());

        $form->addField(new InputField(
            "submit",
            "", //Don't set a name, we don't want this to show up in POST data
            "Save Changes",
            "submit",
            "Save Changes",
            "",
            "uk-button uk-button-primary")
        );

        $alert = array();

        //Check if a POST request has been made
        if($request->isMethod('POST')) {
            $form->validate();

            //Make sure no errors have occured during validation
            if($form->hasErrors() === false) {
                foreach($form->getFields() as $field) {
                    if(!empty($field->getName())) {
                        $config = App::config('shoutzor')->set($field->getName(), $field->getValue());
                    }
                }

                //Do stuff
                $alert = array('type' => 'success', 'msg' => __('Changes saved. Make sure the applicable liquidsoap scripts are restarted for the changes to take effect'));
            }
            //Errors have occured, show error box
            else
            {
                $alert = array('type' => 'error', 'msg' => __('Not all fields passed validation, correct the problems and try again'));
            }
        }

        $content = $form->render();

        return [
            '$view' => [
                'title' => __('Shoutzor Settings'),
                'name'  => 'shoutzor:views/admin/shoutzor.php'
            ],
            'form' => $content,
            'alert' => $alert
        ];
    }
}
