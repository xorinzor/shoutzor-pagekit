<?php $view->script('settings', 'shoutzor:app/bundle/settings.js', ['vue', 'jquery']) ?>

<div id="settings" class="uk-form uk-form-horizontal">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'Playlist Settings' | trans }}</h2>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Switch to the next track' | trans }}</label>
        <div class="uk-form-controls">
            <button type="button" class="uk-button uk-button-primary" v-on="click: nexttrack">{{ 'Next track' | trans }}</button>
        </div>
    </div>

</div>
