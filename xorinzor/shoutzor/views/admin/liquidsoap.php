<?php $view->script('settings', 'shoutzor:app/bundle/settings.js', ['vue', 'jquery']) ?>
<?php $view->style('admin_style', 'shoutzor:assets/css/admin.css'); ?>

<div id="settings" class="uk-form uk-form-horizontal">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove">{{ 'Liquidsoap Settings' | trans }}</h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" v-on="click: save">{{ 'Save' | trans }}</button>
            <button class="uk-button uk-button-danger" v-on="click: save">{{ 'Save & auto-restart' | trans }}</button>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Liquidsoap Wrapper Status' | trans }}</label>
        <div class="uk-form-controls">
            <div class="uk-badge uk-badge-success">Running</div>
            <button type="button" class="uk-button uk-button-danger" v-on="click: stopliquidsoap">{{ 'Stop Wrapper' | trans }}</button>
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
            <p><strong>Important!</strong> Any changes made while LiquidSoap or VLC is running will require a restart to take effect.</p>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Enable Wrapper StdOut' | trans }}</label>
        <div class="uk-form-controls">
            <input type="checkbox" v-model="config.liquidsoap.stdout.wrapper" />
            <span class="uk-form-help-inline">Enable/Disable output to the console window.</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Wrapper log path' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" v-model="config.liquidsoap.logpath.wrapper" />
            <span class="uk-form-help-inline">Enter /dev/null if you do not wish to have a log file.</span>
        </div>
    </div>

    <hr />

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Enable Wrapper socket' | trans }}</label>
        <div class="uk-form-controls">
            <input type="checkbox" v-model="config.liquidsoap.socket.wrapper" />
            <span class="uk-form-help-inline">Disabling this will also disable all online management features in this admin panel.</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Wrapper socket path' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" v-model="config.liquidsoap.socketpath.wrapper" />
            <span class="uk-form-help-inline">Make sure this file can be created automatically and written to.</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Socket file permission' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" v-model="config.liquidsoap.socketpermission" />
            <span class="uk-form-help-inline">Try to keep this as limited as possible.</span>
        </div>
    </div>

    <hr />

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream video fallback' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.liquidsoap.stream.video.placeholder" />
            <span class="uk-form-help-inline">The video to play when no input is received</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream video width' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.liquidsoap.stream.video.width" />
            <span class="uk-form-help-inline">The height for the output video stream (if any), recommended: 1920</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream video height' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.liquidsoap.stream.video.height" />
            <span class="uk-form-help-inline">The height for the output video stream (if any), recommended: 1080</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream video FPS' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.liquidsoap.stream.video.fps" />
            <span class="uk-form-help-inline">The framerate for the output video stream (if any), recommended: 30 or 60</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream audio bitrate' | trans }}</label>
        <div class="uk-form-controls">
            <select name="allow_uploads" class="uk-form-select" v-model="config.liquidsoap.bitrate">
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
            <input type="text" class="uk-form-width-small" v-model="config.liquidsoap.stream.video.logo.width" />
            <span class="uk-form-help-inline">The height for the logo on the video stream (if any)</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream video logo height' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.liquidsoap.stream.video.logo.height" />
            <span class="uk-form-help-inline">The height for the logo on the video stream (if any)</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream video logo file' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-large" v-model="config.liquidsoap.stream.video.logo.path" />
            <span class="uk-form-help-inline">The relative path to the logo file</span>
        </div>
    </div>

    <hr />

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream input mount' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-medium" v-model="config.liquidsoap.stream.input.mount" />
            <span class="uk-form-help-inline">The mount path for the input stream</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream input port' | trans }}</label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-small" v-model="config.liquidsoap.stream.input.port" />
            <span class="uk-form-help-inline">The port for the input stream, default is 1337 but any free port to listen to on this machine will work</span>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Stream input password' | trans }}</label>
        <div class="uk-form-controls">
            <input type="password" class="uk-form-width-large" v-model="config.liquidsoap.stream.input.password" />
            <span class="uk-form-help-inline">The password for the input stream</span>
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
