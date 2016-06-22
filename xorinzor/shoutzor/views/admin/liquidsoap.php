<?php $view->script('settings', 'shoutzor:app/bundle/settings.js', ['vue', 'jquery']) ?>

<div id="settings" class="uk-form uk-form-horizontal">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'Liquidsoap Settings' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Log path' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-large" v-model="config.root_path" />
            <span class="uk-form-help-inline">The path for log files to be written to</span>
        </div>
    </div>

</div>
