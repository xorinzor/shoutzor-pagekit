<div id="settings" class="uk-form uk-form-horizontal">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove"><?= __('Liquidsoap Settings'); ?></h2>
        </div>
        <div data-uk-margin>
            <button class="uk-button uk-button-primary" v-on="click: save"><?= __('Save'); ?></button>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label"><?= __('Log path'); ?></label>
        <div class="uk-form-controls">
            <input type="text" class="uk-form-width-large" />
            <span class="uk-form-help-inline">The path for log files to be written to</span>
        </div>
    </div>

</div>
