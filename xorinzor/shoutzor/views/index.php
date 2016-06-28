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
        <p>Recently played</p>
    </div>

    <?php if(count($history) == 0): ?>
        <p>No content has been played yet!</p>
    <?php else: ?>
        <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Artist</th>
                    <th>Played at</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($history as $item): ?>
                    <tr>
                        <td>
                            <?= $item->media->title; ?>
                        </td>
                        <td>
                            <?= $item->media->artist; ?>
                        </td>
                        <td>
                            <?= $item->played_at->format('d-m-Y h:i'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
