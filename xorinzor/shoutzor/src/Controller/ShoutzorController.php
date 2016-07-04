<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\FormBuilder\FormGenerator;
use Xorinzor\Shoutzor\App\FormBuilder\FormValidation;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\InputField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\SelectField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\DivField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\DividerField;

/**
 * @Access(admin=true)
 */
class ShoutzorController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {

        $request = App::request();

        $config = App::module('shoutzor')->config('shoutzor');
        $config = array_merge($config, $_POST);

        $form = new FormGenerator('', 'POST', 'uk-form uk-form-horizontal');

        $form->addField(new DivField(
            "Permission Check",
            $config['mediaDir'] . ((is_writable($config['mediaDir'])) ? " is writable" : " is not writable! chown manually to www-data:www-data"),
            "",
            (is_writable($config['mediaDir'])) ? "uk-alert uk-alert-success" : "uk-alert uk-alert-danger")
        );

        $form->addField(new DivField(
            "Permission Check",
            App::module('shoutzor')->config('root_path') . $config['imageDir'] . ((is_writable(App::module('shoutzor')->config('root_path') . $config['imageDir'])) ? " is writable" : " is not writable! chown manually to www-data:www-data"),
            "",
            (is_writable(App::module('shoutzor')->config('root_path') . $config['imageDir'])) ? "uk-alert uk-alert-success" : "uk-alert uk-alert-danger")
        );

        $form->addField(new DividerField());

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
            "mediaDir",
            "mediaDir",
            "Media Storage Directory",
            "text",
            $config['mediaDir'],
            "The directory where uploads should be stored",
            "uk-form-width-large")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "imageDir",
            "imageDir",
            "Image Storage Directory",
            "text",
            $config['imageDir'],
            "The directory where downloaded images should be stored",
            "uk-form-width-large")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "parserLastRun",
            "parserLastRun",
            "Parser Last Run Timestamp",
            "text",
            $config['parserLastRun'],
            "The timestamp of when the parser last ran - in general you will not have to make any changes to this value")
        )->setValidationType(FormValidation::TYPE_NUMERIC)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "parserMaxItems",
            "parserMaxItems",
            "Parser Max Items",
            "text",
            $config['parserMaxItems'],
            "The maximum amount of items the parser should parse on each run")
        )->setValidationType(FormValidation::TYPE_NUMERIC)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new DividerField());

        $form->addField(new InputField(
            "userRequestDelay",
            "userRequestDelay",
            "User Request Delay",
            "text",
            $config['userRequestDelay'],
            "The delay in minutes that a user has to wait to be able to request a media object again")
        )->setValidationType(FormValidation::TYPE_NUMERIC)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "mediaRequestDelay",
            "mediaRequestDelay",
            "Media Request Delay",
            "text",
            $config['mediaRequestDelay'],
            "The delay in minutes before a media object can be played again")
        )->setValidationType(FormValidation::TYPE_NUMERIC)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "artistRequestDelay",
            "artistRequestDelay",
            "Artist Request Delay",
            "text",
            $config['artistRequestDelay'],
            "The delay in minutes before a media object from the same artist can be played again")
        )->setValidationType(FormValidation::TYPE_NUMERIC)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
        "uploadDurationLimit",
        "uploadDurationLimit",
        "Media Duration Limit (Minutes)",
        "text",
        $config['uploadDurationLimit'],
        "The limit of the duration from uploaded media files in minutes - changing this will have no effect on already uploaded files")
        )->setValidationType(FormValidation::TYPE_NUMERIC)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new SelectField(
            "useFilenameIfUntitled",
            "useFilenameIfUntitled",
            "Use Filename If Untitled",
            "text",
            $config['useFilenameIfUntitled'],
            array(['value' => 0, 'title' => 'Disabled'], ['value' => 1, 'title' => 'Enabled']),
            false,
            "Use the filename as title when no title could be detected and/or found")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

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

                App::config('shoutzor')->set('shoutzor', $configValues);

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
