<?php $view->style('visualizer', 'shoutzor:assets/css/visualizer.css') ?>
<?php $view->script('shoutzor1', 'shoutzor:assets/js/visualizer/minivents.min.js'); ?>
<?php $view->script('shoutzor2', 'shoutzor:assets/js/jquery-1.9.1.min.js'); ?>
<?php $view->script('shoutzor3', 'shoutzor:assets/js/visualizer/Detector.js'); ?>
<?php $view->script('shoutzor4', 'shoutzor:assets/js/visualizer/three.min.js'); ?>
<?php $view->script('shoutzor5', 'shoutzor:assets/js/visualizer/stats.min.js'); ?>
<?php $view->script('shoutzor6', 'shoutzor:assets/js/visualizer/dat.gui.min.js'); ?>
<?php $view->script('shoutzor7', 'shoutzor:assets/js/visualizer/SimplexNoise.js'); ?>
<?php $view->script('shoutzor8', 'shoutzor:assets/js/visualizer/atutil.js'); ?>
<?php $view->script('shoutzor9', 'shoutzor:assets/js/visualizer/AdditiveBlendShader.js'); ?>
<?php $view->script('shoutzor10', 'shoutzor:assets/js/visualizer/BadTVShader.js'); ?>
<?php $view->script('shoutzor11', 'shoutzor:assets/js/visualizer/EffectComposer.js'); ?>
<?php $view->script('shoutzor12', 'shoutzor:assets/js/visualizer/RenderPass.js'); ?>
<?php $view->script('shoutzor13', 'shoutzor:assets/js/visualizer/ShaderPass.js'); ?>
<?php $view->script('shoutzor14', 'shoutzor:assets/js/visualizer/MaskPass.js'); ?>
<?php $view->script('shoutzor15', 'shoutzor:assets/js/visualizer/BloomPass.js'); ?>
<?php $view->script('shoutzor16', 'shoutzor:assets/js/visualizer/CopyShader.js'); ?>
<?php $view->script('shoutzor17', 'shoutzor:assets/js/visualizer/ConvolutionShader.js'); ?>
<?php $view->script('shoutzor18', 'shoutzor:assets/js/visualizer/HorizontalBlurShader.js'); ?>
<?php $view->script('shoutzor19', 'shoutzor:assets/js/visualizer/VerticalBlurShader.js'); ?>
<?php $view->script('shoutzor20', 'shoutzor:assets/js/visualizer/MirrorShader.js'); ?>
<?php $view->script('shoutzor21', 'shoutzor:assets/js/visualizer/RGBShiftShader.js'); ?>
<?php $view->script('shoutzor22', 'shoutzor:assets/js/visualizer/FilmShader.js'); ?>
<?php $view->script('shoutzor23', 'shoutzor:assets/js/visualizer/Main.js'); ?>
<?php $view->script('shoutzor24', 'shoutzor:assets/js/visualizer/AudioHandler.js'); ?>
<?php $view->script('shoutzor25', 'shoutzor:assets/js/visualizer/ControlsHandler.js'); ?>
<?php $view->script('shoutzor26', 'shoutzor:assets/js/visualizer/FXHandler.js'); ?>
<?php $view->script('shoutzor27', 'shoutzor:assets/js/visualizer/VizHandler.js'); ?>
<?php $view->script('shoutzor28', 'shoutzor:assets/js/visualizer/Bars.js'); ?>
<?php $view->script('shoutzor29', 'shoutzor:assets/js/visualizer/WhiteRing.js'); ?>
<?php $view->script('shoutzor31', 'shoutzor:assets/js/visualizer/helvetiker_bold.typeface.js'); ?>

<html>
    <head>
        <?= $view->render('head') ?>
    </head>

    <body>
        <div id="preloader"></div>

        <div id="viz" style="top: 0px; left: 0;"></div>

        <div id="controls">
            <div id="debugText"></div>
            <canvas id="audioDebug" width="250" height="200"></canvas>
            <div id="settings"></div>
            <div id="stats"></div>
        </div>

        <div id="info"><!--Shoutz0r visualizer. 'Q' to toggle controls. Drop MP3 file to play it.--></div>
    </body>
</html>