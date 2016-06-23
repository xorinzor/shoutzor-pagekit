<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\App\FormBuilder\FormGenerator;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\InputField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\CheckboxField;
use Xorinzor\Shoutzor\App\FormBuilder\Fields\DividerField;

/**
 * @Access(admin=true)
 */
class LiquidsoapController
{
    public function indexAction()
    {
        $config = App::module('shoutzor')->config()['liquidsoap'];

        $form = new FormGenerator('', 'POST', 'uk-form uk-form-horizontal');

        $form->addField(new InputField(
            "logDirectoryPath",
            "logDirectoryPath",
            "Log Directory Path",
            "text",
            $config['logDirectoryPath'],
            "The directory where to store the logs (without ending slash)")
        );

        $form->addField(new CheckboxField(
            "wrapperLogStdout",
            "wrapperLogStdout",
            "Wrapper Log Stdout",
            array($config['wrapperLogStdout']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Show stdout output in the logs")
        );

        $form->addField(new CheckboxField(
            "wrapperServerTelnet",
            "wrapperServerTelnet",
            "Wrapper Enable Telnet",
            array($config['wrapperServerTelnet']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Enable telnet access to the wrapper")
        );

        $form->addField(new CheckboxField(
            "wrapperServerSocket",
            "wrapperServerSocket",
            "Wrapper Enable Socket",
            array($config['wrapperServerSocket']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Enable socket access to the wrapper - REQUIRED")
        );

        $form->addField(new InputField(
            "wrapperServerSocketPath",
            "wrapperServerSocketPath",
            "Wrapper Socket Path",
            "text",
            $config['wrapperServerSocketPath'],
            "The directory where to create the wrapper socket file (without ending slash)")
        );

        $form->addField(new InputField(
            "wrapperServerSocketPermissions",
            "wrapperServerSocketPermissions",
            "Wrapper Socket Permissions",
            "text", $config['wrapperServerSocketPermissions'],
            "The permissions to set to the created wrapper socket file")
        );

        $form->addField(new DividerField());

        $form->addField(new CheckboxField(
            "shoutzorLogStdout",
            "shoutzorLogStdout",
            "Shoutzor Log Stdout",
            array($config['shoutzorLogStdout']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Show stdout output in the logs")
        );

        $form->addField(new CheckboxField(
            "shoutzorServerTelnet",
            "shoutzorServerTelnet",
            "Shoutzor Enable Telnet",
            array($config['shoutzorServerTelnet']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Enable telnet access to shoutzor")
        );

        $form->addField(new CheckboxField(
            "shoutzorServerSocket",
            "shoutzorServerSocket",
            "Shoutzor Enable Socket",
            array($config['shoutzorServerSocket']),
            array(['value' => 1, 'title' => 'enable'],['value' => 0, 'title' => 'disable']),
            false,
            "Enable socket access to shoutzor - REQUIRED")
        );

        $form->addField(new InputField(
            "shoutzorServerSocketPath",
            "shoutzorServerSocketPath",
            "Shoutzor Socket Path",
            "text",
            $config['shoutzorServerSocketPath'],
            "The directory where to create the shoutzor socket file (without ending slash)")
        );

        $form->addField(new InputField(
            "shoutzorServerSocketPermissions",
            "shoutzorServerSocketPermissions",
            "Shoutzor Socket Permissions",
            "text",
            $config['shoutzorServerSocketPermissions'],
            "The permissions to set to the created shoutzor socket file")
        );

        $form->addField(new DividerField());

        $form->addField(new InputField(
            "wrapperInputListeningMount",
            "wrapperInputListeningMount",
            "Wrapper Input Listening Mount",
            "text",
            $config['wrapperInputListeningMount'],
            "The mount that the wrapper and shoutzor should be using to communicate locally")
        );

        $form->addField(new InputField(
            "wrapperInputListeningPort",
            "wrapperInputListeningPort",
            "Wrapper Input Listening Port",
            "text",
            $config['wrapperInputListeningPort'],
            "The port the wrapper and shoutzor should be using to communicate locally")
        );

        $form->addField(new InputField(
            "wrapperInputListeningPassword",
            "wrapperInputListeningPassword",
            "Wrapper Input Listening Password",
            "password",
            $config['wrapperInputListeningPassword'],
            "The password the wrapper and shoutzor should be using to communicate locally")
        );

        $form->addField(new DividerField());

        $form->addField(new InputField(
            "wrapperOutputHost",
            "wrapperOutputHost",
            "Wrapper Output Host",
            "text",
            $config['wrapperOutputHost'],
            "The IP of the icecast server to stream to")
        );

        $form->addField(new InputField(
            "wrapperOutputMount",
            "wrapperOutputMount",
            "Wrapper Output Mount",
            "text",
            $config['wrapperOutputMount'],
            "The mount of the icecast server to stream to")
        );

        $form->addField(new InputField(
            "wrapperOutputPort",
            "wrapperOutputPort",
            "Wrapper Output Port",
            "text",
            $config['wrapperOutputPort'],
            "The port of the icecast server to stream to")
        );

        $form->addField(new InputField(
            "wrapperOutputPassword",
            "wrapperOutputPassword",
            "Wrapper Output Password",
            "password",
            $config['wrapperOutputPassword'],
            "The password of the icecast server to stream to")
        );

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
            "submit",
            "Save Changes",
            "submit",
            "Save Changes",
            "",
            "uk-button uk-button-primary")
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
