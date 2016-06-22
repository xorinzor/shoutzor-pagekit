<?php $view->script('jquery', 'shoutzor:assets/js/jquery-1.9.1.min.js') ?>
<?php $view->script('nowplaying', 'shoutzor:assets/js/nowplaying.js', 'jquery') ?>

<div class="uk-panel uk-panel-box music-box">
    <div class="uk-panel-title">
        <p>Now Playing <a href="http://<?= $_SERVER['SERVER_NAME']; ?>:8000/shoutzor.m3u" class="tuneIn uk-button-small uk-button-primary"><i class="uk-icon-play"></i> Tune In</a></p>
    </div>
    <p id="nowplaying">Loading "now playing" information..</p>
</div>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Recently added</p>
    </div>

    <?php if(count($uploaded) == 0): ?>
        <p>No content has been uploaded yet, be the first!</p>
    <?php else: ?>
        <ul class="uk-thumbnav uploaded-content-list uk-grid-width-small-1-2 uk-grid-width-medium-1-4">
            <?php foreach($uploaded as $item): ?>
                <li>
                    <?= $view->render('shoutzor:views/elements/list-item.php', ['item' => $item]); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Popular requests</p>
    </div>

    <?php if(count($requested) == 0): ?>
        <p>No content has been requested yet, be the first!</p>
    <?php else: ?>
        <ul class="uk-thumbnav uploaded-content-list uk-grid-width-small-1-2 uk-grid-width-medium-1-4">
            <?php foreach($requested as $item): ?>
                <li>
                    <?= $view->render('shoutzor:views/elements/list-item.php', ['item' => $item]); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
