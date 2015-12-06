<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?= $view->render('head') ?>

        <?php $view->style('uikit-style', 'theme:css/uikit.almost-flat.min.css') ?>
        <?php $view->style('uikit-search', 'theme:css/components/search.min.css') ?>

        <?php $view->style('shoutzor-theme-style', 'theme:css/theme.css') ?>

        <?php $view->script('jquery', 'theme:js/jquery-1.9.1.js') ?>
        <?php $view->script('uikit-script', 'theme:js/uikit.min.js', 'jquery') ?>
        <?php $view->script('shoutzor-theme-script', 'theme:js/theme.js') ?>
    </head>
    <body>
        <div class="uk-container uk-container-center">

            <nav class="uk-navbar uk-margin-large-bottom">
                <a class="uk-navbar-brand uk-width-medium-1-5 uk-hidden-small" href="<?= $view->url()->get() ?>">Shoutzor</a>

                <div class="uk-navbar-content" id="main-navbar-content">
                    <form class="uk-search uk-margin-remove uk-display-inline-block" action="<?= $view->url('@shoutzor/search') ?>" method="GET" data-uk-search>
                        <input class="uk-search-field" type="search" placeholder="search" name="q">
                    </form>
                </div>

                <div class="uk-navbar-flip">
                    <div class="uk-navbar-content">
                        <a href="#" class="uk-button uk-button-primary">Upload</a>
                        <a href="#" class="uk-button uk-button-dark">Logout</a>
                    </div>
                </div>

                <a href="#offcanvas" class="uk-navbar-toggle uk-visible-small" data-uk-offcanvas></a>
                <div class="uk-navbar-brand uk-navbar-center uk-visible-small">Brand</div>
            </nav>

            <div class="uk-grid">
                <!-- Render menu position -->
                <?php if ($view->menu()->exists('main')) : ?>
                    <div class="uk-width-medium-1-5">
                        <div class="uk-panel uk-panel-box">
                            <?= $view->menu('main', 'navbar.php') ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="uk-width-medium-4-5">
                    <!-- Render system messages -->
                    <?= $view->render('messages') ?>

                    <!-- Render content -->
                    <?= $view->render('content') ?>
                </div>
            </div>
        </div>

        <!-- Insert code before the closing body tag  -->
        <?= $view->render('footer') ?>
    </body>
</html>
