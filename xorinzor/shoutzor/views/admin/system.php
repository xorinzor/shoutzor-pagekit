<?php $view->style('style', 'shoutzor:assets/css/admin.css', 'theme'); ?>
<?php $view->style('pnotify', 'shoutzor:assets/css/pnotify.custom.min.css', 'uikit') ?>

<?php $view->script('shoutzor-api', 'shoutzor:assets/js/api.js', 'jquery') ?>
<?php $view->script('pnotify', 'shoutzor:assets/js/pnotify.custom.min.js', 'jquery') ?>
<?php $view->script('system-js', 'shoutzor:assets/js/admin/system.js', ['pnotify', 'shoutzor-api']); ?>

<div id="settings" class="uk-form uk-form-horizontal">
    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove"><?= __('Shoutzor System'); ?></h2>
        </div>
    </div>

    <?= $form; ?>

</div>
