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
        <label class="uk-form-label">{{ 'Liquidsoap Service Status' | trans }}</label>
        <div class="uk-form-controls">
            <div class="uk-badge uk-badge-success">Running</div>
            <button type="button" class="uk-button uk-button-danger" v-on="click: nexttrack">{{ 'Stop LiquidSoap' | trans }}</button>
        </div>
    </div>

    <div class="uk-form-row">
        <div class="uk-alert uk-alert-warning">
            <p><strong>Important!</strong> Any changes made while LiquidSoap is running will require a restart to take effect.</p>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Volume Normalization method' | trans }}</label>
        <div class="uk-form-controls">
            <select name="allow_uploads" class="uk-form-select" v-model="config.default">
                <option value="0">None</option>
                <option value="1">Normalization</option>
                <option value="2" selected>Replay Gain</option>
            </select>
        </div>
        <p><small>
            <strong>None:</strong> Audio will be played as-is, sudden volume changes are possible depending on the audio files.<br />
            <strong>Normalization:</strong> This operator cannot guess the volume of the whole stream, and can be &quot;surprised&quot; by rapid changes of the volume. This can lead to a volume that is too low, too high, oscillates.<br />
            <strong>Replay Gain (Recommended):</strong> Computes the loudness based on how the human ear actually perceives each range of frequency. Having computed the average perceived loudness on a track or an album, it is easy to renormalize the tracks when playing, ensuring a comfortable, consistent listening experience.
        </small></p>
    </div>

</div>
