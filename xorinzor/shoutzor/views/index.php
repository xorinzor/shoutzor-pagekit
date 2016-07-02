<?php $view->script('jquery', 'shoutzor:assets/js/jquery-1.9.1.min.js') ?>
<?php $view->script('dateformat', 'shoutzor:assets/js/dateFormat.js', 'jquery') ?>
<?php $view->script('jsrender', 'shoutzor:assets/js/jsrender.js', 'jquery') ?>
<?php $view->script('nowplaying', 'shoutzor:assets/js/nowplaying.js', 'jquery') ?>
<?php $view->script('dashboard', 'shoutzor:assets/js/dashboard.js', ['jsrender', 'dateformat']) ?>

<div class="uk-panel uk-panel-box music-box">
    <div class="uk-panel-title">
        <p>Now Playing <a href="<?= $m3uFile; ?>" class="tuneIn uk-button-small uk-button-primary"><i class="uk-icon-play"></i> Tune In</a></p>
    </div>
    <p id="nowplaying">Loading "now playing" information..</p>
</div>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Recently played</p>
    </div>

    <?= $view->render('shoutzor:views/elements/history-table.php'); ?>
</div>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Request queue</p>
    </div>

    <?= $view->render('shoutzor:views/elements/queue-table.php'); ?>
</div>

<script id="dashboard-table-row-template" type="text/x-jsrender">
<tr>
    <td class="title">{{:title}}</td>
    <td class="artist">{{:artist}}</td>
    <td class="album">{{:album}}</td>
    <td class="duration">{{:duration}}</td>
</tr>
</script>
