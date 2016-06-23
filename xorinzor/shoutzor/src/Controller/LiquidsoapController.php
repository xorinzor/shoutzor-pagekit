<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\FormBuilder\FormGenerator;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\InputField;

/**
 * @Access(admin=true)
 */
class LiquidsoapController
{
    public function indexAction()
    {

        $form = new FormGenerator();
        $form->addField(new InputField("logPath", "logpath", "Log Path", "text", "/tmp/shoutzor", "The directory where to store the logs"));

        $content = $form->render();

        return [
            '$view' => [
                'title' => __('Liquidsoap Settings'),
                'name'  => 'shoutzor:views/admin/liquidsoap.php'
            ],
            'form' => $content
        ];
    }
}
