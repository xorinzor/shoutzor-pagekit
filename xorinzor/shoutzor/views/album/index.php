<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Album List</p>
    </div>

    <?php if(count($albums) == 0): ?>
        <p>No albums are stored in the database yet</p>
    <?php else: ?>
        <ul class="">
            <?php foreach($albums as $album): ?>
                <li>
                    <?= $album->title; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
