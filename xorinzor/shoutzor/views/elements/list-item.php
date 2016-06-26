<figure class="uk-overlay">
    <img src="<?= $view->url()->getStatic('shoutzor:assets/images/music-placeholder.png'); ?>">

    <figcaption class="uk-overlay-panel">
        <h3><a href="#" data-music="<?= $item->id; ?>"><?= $item->title ?></a></h3>
        <?php if($item->artist): ?>
            <p><?= $item->artist->name ?></p>
        <?php endif; ?>
    </figcaption>
</figure>
