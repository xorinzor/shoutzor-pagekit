<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\Liquidsoap\LiquidsoapManager;
use Xorinzor\Shoutzor\App\FormBuilder\FormGenerator;
use Xorinzor\Shoutzor\App\FormBuilder\FormValidation;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\InputField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\DivField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\DividerField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\CheckboxField;

use Exception;

/**
 * @Access(admin=true)
 */
class AcoustidController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        $request = App::request();

        $config = App::module('shoutzor')->config('acoustid');
        $config = array_merge($config, $_POST); //Set the value to the new POST data

        $liquidsoapManager = new LiquidsoapManager();

        $form = new FormGenerator('', 'POST', 'uk-form uk-form-horizontal');

        $form->addField(new DivField(
            "Notice",
            "Make sure to have provided a working Application API Key before enabling AcoustID to prevent any issues",
            "",
            "uk-alert uk-alert-info")
        );

        $form->addField(new CheckboxField(
            "enabled",
            "enabled",
            "Enable AcoustID",
            array($config['enabled']),
            array(['value' => "1", 'title' => 'enabled'],['value' => "0", 'title' => 'disabled']),
            false,
            "Enable the AcoustID Media Fingerprinting Integration")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY, FormValidation::REQ_VALUE => array('1','0')));

        $form->addField(new InputField(
            "appKey",
            "appKey",
            "Application API Key",
            "text",
            $config['appKey'],
            "The Application Key for AcoustID, if you don't have one, get one at: <a href='https://acoustid.org/my-applications'>https://acoustid.org/my-applications</a>")
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
                $configValues = array();

                foreach($form->getFields() as $field) {
                    if(!empty($field->getName())) {
                        $configValues[$field->getName()] = $field->getValue();
                    }
                }

                //Save our config changes
                App::config('shoutzor')->set('acoustid', $configValues);

                //Show success message
                $alert = array('type' => 'success', 'msg' => __('Changes saved'));
            }
            //Errors have occured, show error message
            else
            {
                $alert = array('type' => 'error', 'msg' => __('Not all fields passed validation, correct the problems and try again'));
            }
        }

        $content = $form->render();

        return [
            '$view' => [
                'title' => __('Shoutzor AcoustID'),
                'name'  => 'shoutzor:views/admin/acoustid.php'
            ],
            'form' => $content,
            'alert' => $alert
        ];
    }
}
