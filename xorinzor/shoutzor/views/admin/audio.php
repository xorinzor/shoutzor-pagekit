<?php $view->script('settings', 'shoutzor:app/bundle/settings.js', ['vue', 'jquery']) ?>

<div id="settings" class="uk-form uk-form-horizontal">
    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove"><?= __('Audio Settings'); ?></h2>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label"><?= __('Play Music'); ?></label>
        <div class="uk-form-controls">
            <button type="button" class="uk-button uk-button-primary" v-on="click: play"><?= __('Play Music'); ?></button>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label"><?= __('Pause Music'); ?></label>
        <div class="uk-form-controls">
            <button type="button" class="uk-button uk-button-primary" v-on="click: pause"><?= __('Pause Music'); ?></button>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label"><?= __('Switch to the next track'); ?></label>
        <div class="uk-form-controls">
            <button type="button" class="uk-button uk-button-primary" v-on="click: nexttrack"><?= __('Next track'); ?></button>
        </div>
    </div>
</div>
