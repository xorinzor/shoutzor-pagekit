<?php $view->style('style', 'shoutzor:assets/css/admin.css', 'theme'); ?>

<div id="settings" class="uk-form uk-form-horizontal">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>
            <h2 class="uk-margin-remove"><?= __('Liquidsoap Settings'); ?></h2>
        </div>
    </div>

    <?php
        if(isset($alert['type'])) {
            if($alert['type'] == 'error') {
                echo '<div class="uk-alert uk-alert-danger">' . $alert['msg'] . '</div>';
            }

            if($alert['type'] == 'success') {
                echo '<div class="uk-alert uk-alert-success">' . $alert['msg'] . '</div>';
            }
        }

        echo $form;
    ?>

</div>
