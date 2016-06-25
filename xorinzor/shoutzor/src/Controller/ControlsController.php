<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\Liquidsoap\LiquidsoapManager;
use Xorinzor\Shoutzor\App\FormBuilder\FormGenerator;
use Xorinzor\Shoutzor\App\FormBuilder\FormValidation;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\InputField;

use Exception;

/**
 * @Access(admin=true)
 */
class ControlsController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        $config = App::module('shoutzor')->config('liquidsoap');

        $liquidsoapManager = new LiquidsoapManager();

        $liquidsoapManager->startScript('wrapper');

        $wrapperActive = $liquidsoapManager->isUp('wrapper');
        $shoutzorActive = false;
        //$shoutzorActive = $liquidsoapManager->isUp('shoutzor');

        $form = new FormGenerator('', 'POST', 'uk-form uk-form-horizontal');

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
                'title' => __('Shoutzor Controls'),
                'name'  => 'shoutzor:views/admin/controls.php'
            ],
            'form' => $content
        ];
    }

    /**
     * @Route("/toggle", name="toggle", requirements={"type"="wrapper|shoutzor","operation"="start|stop"}, methods="POST")
     */
    public function toggleAction($type) {

    }
}
