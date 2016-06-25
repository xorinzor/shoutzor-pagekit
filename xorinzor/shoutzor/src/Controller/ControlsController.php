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
class ControlsController
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

        $form = new FormGenerator('', 'POST', 'uk-form uk-form-horizontal');

        $form->addField(new InputField(
            "playMusic",
            "playMusic",
            "Play Music",
            "button",
            "Play Music",
            "Sends the &quot;play&quot; command",
            "uk-button uk-button-primary",
            'onclick="playMusic();"')
        );

        $form->addField(new InputField(
            "pauseMusic",
            "pauseMusic",
            "Pause Music",
            "button",
            "Pause Music",
            "Sends the &quot;pause&quot; command",
            "uk-button uk-button-primary",
            'onclick="pauseMusic();"')
        );

        $form->addField(new InputField(
            "skipTrack",
            "skipTrack",
            "Skip Track",
            "button",
            "Skip Track",
            "Sends the &quot;skip track&quot; command",
            "uk-button uk-button-primary",
            'onclick="skipTrack();"')
        );

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
