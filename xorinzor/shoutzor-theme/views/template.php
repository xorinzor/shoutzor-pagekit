<?php
    use Pagekit\Application as App;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?= $view->render('head') ?>

        <link rel="apple-touch-icon" sizes="57x57" href="<?= $view->url()->getStatic('theme:images/apple-icon-57x57.png'); ?>">
        <link rel="apple-touch-icon" sizes="60x60" href="<?= $view->url()->getStatic('theme:images/apple-icon-60x60.png'); ?>">
        <link rel="apple-touch-icon" sizes="72x72" href="<?= $view->url()->getStatic('theme:images/apple-icon-72x72.png'); ?>">
        <link rel="apple-touch-icon" sizes="76x76" href="<?= $view->url()->getStatic('theme:images/apple-icon-76x76.png'); ?>">
        <link rel="apple-touch-icon" sizes="114x114" href="<?= $view->url()->getStatic('theme:images/apple-icon-114x114.png'); ?>">
        <link rel="apple-touch-icon" sizes="120x120" href="<?= $view->url()->getStatic('theme:images/apple-icon-120x120.png'); ?>">
        <link rel="apple-touch-icon" sizes="144x144" href="<?= $view->url()->getStatic('theme:images/apple-icon-144x144.png'); ?>">
        <link rel="apple-touch-icon" sizes="152x152" href="<?= $view->url()->getStatic('theme:images/apple-icon-152x152.png'); ?>">
        <link rel="apple-touch-icon" sizes="180x180" href="<?= $view->url()->getStatic('theme:images/apple-icon-180x180.png'); ?>">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?= $view->url()->getStatic('theme:images/android-icon-192x192.png'); ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= $view->url()->getStatic('theme:images/favicon-32x32.png'); ?>">
        <link rel="icon" type="image/png" sizes="96x96" href="<?= $view->url()->getStatic('theme:images/favicon-96x96.png'); ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= $view->url()->getStatic('theme:images/favicon-16x16.png'); ?>">
        <link rel="manifest" href="<?= $view->url()->getStatic('theme:images/manifest.json'); ?>">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="<?= $view->url()->getStatic('theme:images/ms-icon-144x144.png'); ?>">
        <meta name="theme-color" content="#ffffff">

        <?php $view->style('uikit', 'theme:css/uikit.almost-flat.min.css') ?>
        <?php $view->style('uikit-search', 'theme:css/components/search.min.css', 'uikit') ?>

        <?php $view->style('pnotify', 'shoutzor:assets/css/pnotify.custom.min.css', 'uikit') ?>
        <?php $view->style('shoutzor-theme', 'theme:css/theme.css', 'pnotify') ?>

        <?php $view->script('jquery', 'theme:js/jquery-1.9.1.js') ?>
        <?php $view->script('uikit-script', 'theme:js/uikit.min.js', 'jquery') ?>
        <?php $view->script('pnotify', 'shoutzor:assets/js/pnotify.custom.min.js', 'jquery') ?>
        <?php $view->script('shoutzor-api', 'shoutzor:assets/js/api.js', 'jquery') ?>
        <?php $view->script('shoutzor-theme', 'shoutzor:assets/js/theme.js', ['jquery', 'shoutzor-api']) ?>
    </head>
    <body>
        <div class="uk-container uk-container-center">

            <?php if(!is_null(App::user()->id)): ?>
                <nav class="uk-navbar uk-margin-large-bottom">
                    <a class="uk-navbar-brand uk-width-medium-1-5 uk-hidden-small" href="<?= $view->url()->get() ?>">
                        <img src="<?= $view->url()->getStatic('theme:images/shoutzor-logo-small.png'); ?>" alt="shoutz0r logo" />
                    </a>

                    <div class="uk-navbar-content" id="main-navbar-content">
                            <form class="uk-search uk-margin-remove uk-display-inline-block" action="<?= $view->url('@shoutzor/search') ?>" method="GET" data-uk-search>
                                <input class="uk-search-field" type="search" placeholder="search" name="q">
                            </form>
                    </div>

                    <div class="uk-navbar-flip">
                        <div class="uk-navbar-content">
                            <a href="<?= $view->url('@shoutzor/uploadmanager'); ?>" class="uk-button uk-button-primary"><i class="uk-icon-upload"></i> Upload Music / Videos</a>
                            <a href="<?= $view->url('@user/logout') ?>" class="uk-button uk-button-dark"><i class="uk-icon-power-off"></i> Logout</a>
                        </div>
                    </div>

                    <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
                    <div class="uk-navbar-brand uk-navbar-center uk-visible-small">Shoutz0r</div>
                </nav>
            <?php endif; ?>

            <div class="uk-grid">
                <!-- Render menu position -->
                <?php if ($view->menu()->exists('main') && !is_null(App::user()->id)) : ?>
                    <div class="uk-width-medium-1-5">
                        <div class="uk-panel uk-panel-box">
                            <?= $view->menu('main', 'navbar.php') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(!is_null(App::user()->id)): ?>
                    <div class="uk-width-medium-4-5">

                        <!-- Render system messages -->
                        <?= $view->render('messages') ?>

                <?php else: ?>
                    <div class="uk-width-medium-1-1">
                <?php endif; ?>
                    
                    <!-- Render content -->
                    <?= $view->render('content') ?>
                </div>
            </div>
        </div>

        <!-- Insert code before the closing body tag  -->
        <?= $view->render('footer') ?>
    </body>
</html>
