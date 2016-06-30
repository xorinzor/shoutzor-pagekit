<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Artists List</p>
    </div>

    <?php if(count($artists) == 0): ?>
        <p>No artists are stored in the database yet</p>
    <?php else: ?>
        <ul class="">
            <?php foreach($artists as $artist): ?>
                <li>
                    <?= $artist->name; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
