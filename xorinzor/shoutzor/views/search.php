<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Search Results for '<?= $searchterm; ?>' - Found <?= $total; ?> Result(s)</p>
    </div>

    <ul class="uk-list uk-list-line">
        <?php foreach($results as $item): ?>
            <li>
                <div class="search-result">
                    <figure class="uk-overlay">
                        <?php if($item->is_video): ?>
                            <img src="<?= $item->thumbnail; ?>">
                        <?php else: ?>
                            <img src="<?= $view->url()->getStatic('shoutzor:assets/images/music-placeholder.png'); ?>">
                        <?php endif; ?>
                    </figure>
                    <p><strong><?= $item->title; ?></strong> - <?php if($item->artist) { echo $item->artist->name; } ?></p>
                    <a data-music="<?= $item->id; ?>" class="uk-button uk-button-primary"><i class="uk-icon uk-icon-plus"></i> Request</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>