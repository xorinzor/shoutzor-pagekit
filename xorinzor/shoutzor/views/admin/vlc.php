<?php $view->script('settings', 'shoutzor:app/bundle/settings.js', ['vue', 'jquery']) ?>
<?php $view->style('admin_style', 'shoutzor:assets/css/admin.css'); ?>

<div id="settings" class="uk-form uk-form-horizontal">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'VLC Settings' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>
            <button class="uk-button uk-button-danger" v-on="click: save">{{ 'Save & auto-restart' | trans }}</button>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'VLC Stream Status' | trans }}</label>
        <div class="uk-form-controls">
            <div class="uk-badge uk-badge-success">Running</div>
            <button type="button" class="uk-button uk-button-danger" v-on="click: stopvlc">{{ 'Stop VLC' | trans }}</button>
        </div>
    </div>

    <div class="uk-form-row">
        <div class="uk-alert uk-alert-warning">
            <p><strong>Important!</strong> Any changes made while VLC is running will require a restart to take effect.</p>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream video width' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.vlc.stream.video.width" />
            <span class="uk-form-help-inline">The height for the output video stream (if any), recommended: 1920</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream video height' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.vlc.stream.video.height" />
            <span class="uk-form-help-inline">The height for the output video stream (if any), recommended: 1080</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream audio bitrate' | trans }}</label>
        <div class="uk-form-controls">
            <select name="allow_uploads" class="uk-form-select" v-model="config.vlc.bitrate">
                <option value="96">96kb/s</option>
                <option value="128">128kb/s</option>
                <option value="160">160kb/s</option>
                <option value="192">192kb/s</option>
                <option value="256">256kb/s</option>
                <option value="320">320kb/s</option>
            </select>
            <p class="uk-form-help-block">
                <strong>96kb/s</strong> Low-quality<br />
                <strong>128-192kb/s</strong> Medium-quality<br />
                <strong>256-320kb/s</strong> High-quality
            </p>
        </div>
    </div>

    <hr />

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream video logo width' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.vlc.stream.video.logo.width" />
            <span class="uk-form-help-inline">The height for the logo on the video stream (if any)</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream video logo height' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.vlc.stream.video.logo.height" />
            <span class="uk-form-help-inline">The height for the logo on the video stream (if any)</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream video logo file' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-large" v-model="config.vlc.stream.video.logo.path" />
            <span class="uk-form-help-inline">The relative path to the logo file</span>
        </div>
    </div>

    <hr />

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Telnet port' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.vlc.telnet.port" />
            <span class="uk-form-help-inline">The port for the telnet interface, default is 4212 but any free port to listen to on this machine will work</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Telnet password' | trans }}</label>
        <div class="uk-form-controls">
            <input type="password" class="uk-form-width-large" v-model="config.vlc.telnet.password" />
            <span class="uk-form-help-inline">The password for the telnet interface</span>
        </div>
    </div>

    <hr />

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream output host' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-medium" v-model="config.liquidsoap.stream.output.host" />
            <span class="uk-form-help-inline">The destination host for the output stream, this should be an icecast server (or similar)</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream output mount' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-medium" v-model="config.liquidsoap.stream.output.mount" />
            <span class="uk-form-help-inline">The destination mount path for the output stream</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream output port' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.liquidsoap.stream.output.port" />
            <span class="uk-form-help-inline">The destination port for the output stream, icecast default is 8000</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream output password' | trans }}</label>
        <div class="uk-form-controls">
            <input type="password" class="uk-form-width-large" v-model="config.liquidsoap.stream.output.password" />
            <span class="uk-form-help-inline">The destination password for the output stream</span>
        </div>
    </div>
</div>
