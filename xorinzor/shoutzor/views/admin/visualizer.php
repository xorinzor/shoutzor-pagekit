<?php $view->script('settings', 'shoutzor:app/bundle/settings.js', ['vue', 'jquery']) ?>

<div id="settings" class="uk-form uk-form-horizontal">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'Visualizer Settings' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-form-row">
        <div class="uk-alert uk-alert-warning">
            <p><strong>Important!</strong> Applying changes will cause a brief disruption in the video stream from the visualizer</p>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Enable visualizer' | trans }}</label>
        <div class="uk-form-controls">
            <input type="checkbox" v-model="config.default" />
        </div>
        <p><small>Disabling the visualizer will result in a black screen in the video stream.</small></p>
    </div>

</div>
