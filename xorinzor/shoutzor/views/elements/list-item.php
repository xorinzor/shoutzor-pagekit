<figure class="uk-overlay">
    <?php if($item->is_video): ?>
        <img src="<?= $item->thumbnail; ?>">
    <?php else: ?>
        <img src="<?= $view->url()->getStatic('shoutzor:assets/images/music-placeholder.png'); ?>">
    <?php endif; ?>

    <figcaption class="uk-overlay-panel">
        <h3><?= $item->title ?></h3>
        <?php if($item->artist): ?>
            <p><?= $item->artist->name ?></p>
        <?php endif; ?>
    </figcaption>
</figure>