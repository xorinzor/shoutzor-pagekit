<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\Liquidsoap\LiquidsoapManager;
use Xorinzor\Shoutzor\App\FormBuilder\FormGenerator;
use Xorinzor\Shoutzor\App\FormBuilder\FormValidation;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\InputField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\CheckboxField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\DividerField;

/**
 * @Access(admin=true)
 */
class LiquidsoapController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        $request = App::request();

        $config = App::module('shoutzor')->config('liquidsoap');
        $config = array_merge($config, $_POST); //Set the value to the new POST data

        $form = new FormGenerator('', 'POST', 'uk-form uk-form-horizontal');

        $form->addField(new InputField(
            "logDirectoryPath",
            "logDirectoryPath",
            "Log Directory Path",
            "text",
            $config['logDirectoryPath'],
            "The directory where to store the logs (without ending slash)")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "socketPath",
            "socketPath",
            "Socket Path",
            "text",
            $config['socketPath'],
            "The directory where to create the socket files (without ending slash)")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "socketPermissions",
            "socketPermissions",
            "Socket Permissions",
            "text", $config['socketPermissions'],
            "The permissions to set to the created socket files")
        )->setValidationType(FormValidation::TYPE_NUMERIC)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new DividerField());

        $form->addField(new CheckboxField(
            "wrapperLogStdout",
            "wrapperLogStdout",
            "Wrapper Log Stdout",
            array($config['wrapperLogStdout']),
            array(['value' => "true", 'title' => 'enable'],['value' => "false", 'title' => 'disable']),
            false,
            "Show stdout output in the logs")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY, FormValidation::REQ_VALUE => array('true','false')));

        $form->addField(new CheckboxField(
            "wrapperServerTelnet",
            "wrapperServerTelnet",
            "Wrapper Enable Telnet",
            array($config['wrapperServerTelnet']),
            array(['value' => "true", 'title' => 'enable'],['value' => "false", 'title' => 'disable']),
            false,
            "Enable telnet access to the wrapper")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY, FormValidation::REQ_VALUE => array('true','false')));

        $form->addField(new CheckboxField(
            "wrapperServerSocket",
            "wrapperServerSocket",
            "Wrapper Enable Socket",
            array($config['wrapperServerSocket']),
            array(['value' => "true", 'title' => 'enable'],['value' => "false", 'title' => 'disable']),
            false,
            "Enable socket access to the wrapper - REQUIRED FOR CONTROLS TO WORK")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY, FormValidation::REQ_VALUE => array('true','false')));

        $form->addField(new DividerField());

        $form->addField(new CheckboxField(
            "shoutzorLogStdout",
            "shoutzorLogStdout",
            "Shoutzor Log Stdout",
            array($config['shoutzorLogStdout']),
            array(['value' => "true", 'title' => 'enable'],['value' => "false", 'title' => 'disable']),
            false,
            "Show stdout output in the logs")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY, FormValidation::REQ_VALUE => array('true','false')));

        $form->addField(new CheckboxField(
            "shoutzorServerTelnet",
            "shoutzorServerTelnet",
            "Shoutzor Enable Telnet",
            array($config['shoutzorServerTelnet']),
            array(['value' => "true", 'title' => 'enable'],['value' => "false", 'title' => 'disable']),
            false,
            "Enable telnet access to shoutzor")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY, FormValidation::REQ_VALUE => array('true','false')));

        $form->addField(new CheckboxField(
            "shoutzorServerSocket",
            "shoutzorServerSocket",
            "Shoutzor Enable Socket",
            array($config['shoutzorServerSocket']),
            array(['value' => "true", 'title' => 'enable'],['value' => "false", 'title' => 'disable']),
            false,
            "Enable socket access to shoutzor - REQUIRED FOR CONTROLS TO WORK")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY, FormValidation::REQ_VALUE => array('true','false')));

        $form->addField(new DividerField());

        $form->addField(new InputField(
            "wrapperInputListeningMount",
            "wrapperInputListeningMount",
            "Wrapper Input Listening Mount",
            "text",
            $config['wrapperInputListeningMount'],
            "The mount that the wrapper and shoutzor should be using to communicate locally")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "wrapperInputListeningPort",
            "wrapperInputListeningPort",
            "Wrapper Input Listening Port",
            "text",
            $config['wrapperInputListeningPort'],
            "The port the wrapper and shoutzor should be using to communicate locally")
        )->setValidationType(FormValidation::TYPE_NUMERIC)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "wrapperInputListeningPassword",
            "wrapperInputListeningPassword",
            "Wrapper Input Listening Password",
            "password",
            $config['wrapperInputListeningPassword'],
            "The password the wrapper and shoutzor should be using to communicate locally")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new DividerField());

        $form->addField(new InputField(
            "wrapperOutputHost",
            "wrapperOutputHost",
            "Wrapper Output Host",
            "text",
            $config['wrapperOutputHost'],
            "The IP of the icecast server to stream to")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "wrapperOutputMount",
            "wrapperOutputMount",
            "Wrapper Output Mount",
            "text",
            $config['wrapperOutputMount'],
            "The mount of the icecast server to stream to")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "wrapperOutputPort",
            "wrapperOutputPort",
            "Wrapper Output Port",
            "text",
            $config['wrapperOutputPort'],
            "The port of the icecast server to stream to")
        )->setValidationType(FormValidation::TYPE_NUMERIC)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new InputField(
            "wrapperOutputPassword",
            "wrapperOutputPassword",
            "Wrapper Output Password",
            "password",
            $config['wrapperOutputPassword'],
            "The password of the icecast server to stream to")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new DividerField());

        $form->addField(new InputField(
            "shoutzorUrl",
            "shoutzorUrl",
            "Shoutzor Website URL",
            "text",
            $config['shoutzorUrl'],
            "The hostname of the url this website is running on, example: 'https://shoutzor.com' NO ENDING SLASH")
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        $form->addField(new DividerField());

        $form->addField(new InputField(
            "encodingBitrate",
            "encodingBitrate",
            "Encoding bitrate",
            "text",
            $config['encodingBitrate'],
            "The bitrate of our audio stream")
        );

        $form->addField(new InputField(
            "encodingQuality",
            "encodingQuality",
            "LAME Encoding Quality",
            "text",
            $config['encodingQuality'],
            "The quality to be used by the LAME encoder, 0 - 9 where 0 is the highest quality")
        );

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

                $replace_values = array();
                $configValues = array();

                foreach($form->getFields() as $field) {
                    if(!empty($field->getName())) {
                        $configValues[$field->getName()] = $field->getValue();
                        $replace_values['%'.$field->getName().'%'] = $field->getValue();
                    }
                }

                //Save our config changes
                App::config('shoutzor')->set('liquidsoap', $configValues);

                //Generate our new config file
                $liquidsoapManager = new liquidsoapManager();
                $liquidsoapManager->generateConfigFile($replace_values);

                //Show success message
                $alert = array('type' => 'success', 'msg' => __('Changes saved. Make sure the applicable liquidsoap scripts are restarted for the changes to take effect'));
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
                'title' => __('Liquidsoap Settings'),
                'name'  => 'shoutzor:views/admin/liquidsoap.php'
            ],
            'form' => $content,
            'alert' => $alert
        ];
    }
}
