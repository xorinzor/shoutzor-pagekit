<?php $view->style('shoutzor-style-main', 'shoutzor:assets/css/style.css') ?>

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
                    <figure class="uk-overlay">
                        <img src="http://placehold.it/350x203">
                        <figcaption class="uk-overlay-panel">
                            <h3><?= $item->title ?></h3>
                            <?php if($item->artist): ?>
                                <p><?= $item->artist->name ?></p>
                            <?php endif; ?>
                        </figcaption>
                    </figure>
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
                    <figure class="uk-overlay">
                        <img src="http://placehold.it/350x203">
                        <figcaption class="uk-overlay-panel">
                            <h3><?= $item->title ?></h3>
                            <?php if($item->artist): ?>
                                <p><?= $item->artist->name ?></p>
                            <?php endif; ?>
                        </figcaption>
                    </figure>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>