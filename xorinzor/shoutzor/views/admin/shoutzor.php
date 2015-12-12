<?php $view->script('settings', 'shoutzor:app/bundle/settings.js', ['vue', 'jquery']) ?>

<div id="settings" class="uk-form uk-form-horizontal">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'Shoutzor Settings' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Allow uploads' | trans }}</label>
        <div class="uk-form-controls">
            <select name="allow_uploads" class="uk-form-select" v-model="config.shoutzor.upload">
                <option value="0">None</option>
                <option value="1">Music only</option>
                <option value="2">Videos only</option>
                <option value="3">Music &amp; Videos</option>
            </select>
        </div>
        <p><small>Changing this setting will not delete uploaded content</small></p>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Allow requests' | trans }}</label>
        <div class="uk-form-controls">
            <select name="allow_uploads" class="uk-form-select" v-model="config.shoutzor.request">
                <option value="0">Music only</option>
                <option value="1">Videos only</option>
                <option value="2">Music &amp; Videos</option>
            </select>
        </div>
        <p><small>Changing this setting will only show uploads of the specified type(s)</small></p>
    </div>

</div>
