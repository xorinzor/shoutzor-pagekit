<?php $view->script('jquery', 'shoutzor:assets/js/jquery-1.9.1.min.js') ?>
<?php $view->script('nowplaying', 'shoutzor:assets/js/nowplaying.js', 'jquery') ?>

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

    <?php if(count($history) == 0): ?>
        <p>No content has been played yet!</p>
    <?php else: ?>
        <?= $view->render('shoutzor:views/elements/history-table.php', ['tracks' => $history]); ?>
    <?php endif; ?>
</div>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Request Queue</p>
    </div>

    <?php if(count($queued) == 0): ?>
        <p>No content has been requested yet!</p>
    <?php else: ?>
        <?= $view->render('shoutzor:views/elements/queue-table.php', ['tracks' => $queued, 'starttime' => $starttime]); ?>
    <?php endif; ?>
</div>
