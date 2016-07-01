<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p><?= $album->title; ?> (album)</p>
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
        <p>Contributing Artists</p>
    </div>

    <?php
        $artistList = '';

        foreach($album->artist as $artist) {
            if(!empty($artistList)) {
                $artistList .= ', ';
            }

            $artistList .= '<a href="' . $view->url('@shoutzor/artist/view', ['id' => $artist->id]) . '">' . $artist->name . '</a>';
        }

        echo $artistList;
    ?>
</div>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Tracks in Album</p>
    </div>

    <?= $view->render('shoutzor:views/elements/tracks-table.php', ['tracks' => $tracks]); ?>
</div>
