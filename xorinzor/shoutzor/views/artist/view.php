<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p><?= $artist->name; ?> (artist)</p>
    </div>

    <div id="details" class="uk-grid">
        <div class="uk-width-1-6">
            <img class="item-image" src="<?= $image; ?>" />
        </div>
        <div class="uk-width-5-6 summary">
            <?= $summary; ?>
        </div>
    </div>
</div>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Top Requested Tracks</p>
    </div>

    <?= $view->render('shoutzor:views/elements/tracks-table.php', ['tracks' => $topTracks]); ?>
</div>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Albums</p>
    </div>

    <?= $view->render('shoutzor:views/elements/albums-table.php', ['albums' => $albums]); ?>
</div>
