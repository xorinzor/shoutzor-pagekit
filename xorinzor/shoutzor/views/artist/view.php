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
        <p>Top Tracks</p>
    </div>

    <pre>
        <?php var_dump($artist); ?>
    </pre>
</div>
