<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\Liquidsoap\LiquidsoapManager;
use Xorinzor\Shoutzor\App\FormBuilder\FormGenerator;
use Xorinzor\Shoutzor\App\FormBuilder\FormValidation;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\InputField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\DivField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\DividerField;

use Exception;

/**
 * @Access(admin=true)
 */
class SystemController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        $config = App::module('shoutzor')->config('liquidsoap');

        $liquidsoapManager = new LiquidsoapManager();

        $wrapperActive = $liquidsoapManager->isUp('wrapper');
        $shoutzorActive = $liquidsoapManager->isUp('shoutzor');

        /*
        @TODO move to own API method
        */
        $liquidsoapManager->startScript('wrapper');
        $liquidsoapManager->startScript('shoutzor');

        //IMPORTANT! If not called after start of the script it wont start playing requests
        $liquidsoapManager->command('shoutzor', 'sound.select 0 true');


        $form = new FormGenerator('', 'POST', 'uk-form uk-form-horizontal');

        $form->addField(new DivField(
            "Permission Check",
            $config['logDirectoryPath'] . ((is_writable($config['logDirectoryPath'])) ? " is writable" : " is not writable! chown manually to www-data:www-data"),
            "",
            (is_writable($config['logDirectoryPath'])) ? "uk-alert uk-alert-success" : "uk-alert uk-alert-danger")
        );

        //Usually the log directory and the socket directory will be the same
        //Thus, showing twice that the same directory is (not) writable has no use
        if($config['logDirectoryPath'] != $config['socketPath']) {
            $form->addField(new DivField(
                "Permission Check",
                $config['socketPath'] . ((is_writable($config['socketPath'])) ? " is writable" : " is not writable! chown manually to www-data:www-data"),
                "",
                (is_writable($config['socketPath'])) ? "uk-alert uk-alert-success" : "uk-alert uk-alert-danger")
            );
        }

        $form->addField(new DivField(
            "Permission Check",
            $liquidsoapManager->getPidFileDirectory() . ((is_writable($liquidsoapManager->getPidFileDirectory())) ? " is writable" : " is not writable! chown manually to liquidsoap:www-data"),
            "",
            (is_writable($liquidsoapManager->getPidFileDirectory())) ? "uk-alert uk-alert-success" : "uk-alert uk-alert-danger")
        );

        $form->addField(new DividerField());

        $form->addField(new InputField(
            "wrapperToggle",
            "wrapperToggle",
            ($wrapperActive) ? "Deactivate Wrapper" : "Activate Wrapper",
            "button",
            ($wrapperActive) ? "Deactivate Wrapper" : "Activate Wrapper",
            "(De)activates the wrapper liquidsoap script",
            ($wrapperActive) ? "uk-button uk-button-danger" : "uk-button uk-button-primary",
            'onclick="toggleWrapper();"')
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        if($wrapperActive === false) {
            $form->setError("The wrapper is inactive!");
        } else {
            $form->setSuccess("The wrapper is up and running!");
        }

        $form->addField(new InputField(
            "shoutzorToggle",
            "shoutzorToggle",
            ($shoutzorActive) ? "Deactivate Shoutzor" : "Activate Shoutzor",
            "button",
            ($shoutzorActive) ? "Deactivate Shoutzor" : "Activate Shoutzor",
            "(De)activates the shoutzor liquidsoap script",
            ($shoutzorActive) ? "uk-button uk-button-danger" : "uk-button uk-button-primary",
            'onclick="toggleShoutzor();"')
        )->setValidationType(FormValidation::TYPE_STRING)
        ->setValidationRequirements(array(FormValidation::REQ_NOTEMPTY));

        if($shoutzorActive === false) {
            if($wrapperActive === false) {
                $form->setError("The wrapper needs to be active first!");
            } else {
                $form->setError("Shoutzor is inactive!");
            }
        } else {
            $form->setSuccess("Shoutzor is up and running!");
        }

        $content = $form->render();


        return [
            '$view' => [
                'title' => __('Shoutzor System'),
                'name'  => 'shoutzor:views/admin/system.php'
            ],
            'form' => $content
        ];
    }
}
