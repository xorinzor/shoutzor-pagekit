<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\FormBuilder\FormGenerator;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\InputField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\CheckboxField;

/**
 * @Access(admin=true)
 */
class LiquidsoapController
{
    public function indexAction()
    {
        $config = App::module('shoutzor')->config()['liquidsoap'];

        $form = new FormGenerator();
        $form->addField(new InputField(
            "logDirectoryPath",
            "logDirectoryPath",
            "Log Directory Path",
            "text",
            $config['options']['logDirectoryPath'],
            "The directory where to store the logs (without ending slash)")
        );

        $form->addField(new CheckboxField(
            "wrapperLogStdout",
            "wrapperLogStdout",
            "Wrapper Log Stdout",
            array($config['options']['wrapperLogStdout']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Show stdout output in the logs")
        );

        $form->addField(new CheckboxField(
            "wrapperServerTelnet",
            "wrapperServerTelnet",
            "Wrapper Enable Telnet",
            array($config['options']['wrapperServerTelnet']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Enable telnet access to the wrapper")
        );

        $form->addField(new CheckboxField(
            "wrapperServerSocket",
            "wrapperServerSocket",
            "Wrapper Enable Socket",
            array($config['options']['wrapperServerSocket']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Enable socket access to the wrapper - REQUIRED")
        );

        $form->addField(new InputField(
            "wrapperServerSocketPath",
            "wrapperServerSocketPath",
            "Wrapper Socket Path",
            "text",
            $config['options']['wrapperServerSocketPath'],
            "The directory where to create the wrapper socket file (without ending slash)")
        );

        $form->addField(new InputField(
            "wrapperServerSocketPermissions",
            "wrapperServerSocketPermissions",
            "Wrapper Socket Permissions",
            "text", $config['options']['wrapperServerSocketPermissions'],
            "The permissions to set to the created wrapper socket file")
        );

        $form->addField(new CheckboxField(
            "shoutzorLogStdout",
            "shoutzorLogStdout",
            "Shoutzor Log Stdout",
            array($config['options']['shoutzorLogStdout']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Show stdout output in the logs")
        );

        $form->addField(new CheckboxField(
            "shoutzorServerTelnet",
            "shoutzorServerTelnet",
            "Shoutzor Enable Telnet",
            array($config['options']['shoutzorServerTelnet']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Enable telnet access to shoutzor")
        );

        $form->addField(new CheckboxField(
            "shoutzorServerSocket",
            "shoutzorServerSocket",
            "Shoutzor Enable Socket",
            array($config['options']['shoutzorServerSocket']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Enable socket access to shoutzor - REQUIRED")
        );

        $form->addField(new InputField(
            "shoutzorServerSocketPath",
            "shoutzorServerSocketPath",
            "Shoutzor Socket Path",
            "text",
            $config['options']['shoutzorServerSocketPath'],
            "The directory where to create the shoutzor socket file (without ending slash)")
        );

        $form->addField(new InputField(
            "shoutzorServerSocketPermissions",
            "shoutzorServerSocketPermissions",
            "Shoutzor Socket Permissions",
            "text",
            $config['options']['shoutzorServerSocketPermissions'],
            "The permissions to set to the created shoutzor socket file")
        );

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
