<?php

$statusid = array(
    0 => 'waiting',
    1 => 'processing',
    2 => 'finished',
    3 => 'error',
    4 => 'duplicate'
);

$status = array(
    \Xorinzor\Shoutzor\Model\Music::STATUS_UPLOADED => array(
        'label' => 'Waiting',
        'progressbar' => 'Waiting to be processed..',
        'labelclass' => 'uk-badge-warning',
        'progressbarclass' => 'uk-progress-warning uk-progress-striped uk-active'
    ),
    \Xorinzor\Shoutzor\Model\Music::STATUS_PROCESSING => array(
        'label' => 'Processing',
        'progressbar' => 'Processing..',
        'labelclass' => '',
        'progressbarclass' => 'uk-progress-striped uk-active'
    ),
    \Xorinzor\Shoutzor\Model\Music::STATUS_FINISHED => array(
        'label' => 'Finished',
        'progressbar' => 'Finished',
        'labelclass' => 'uk-badge-success',
        'progressbarclass' => 'uk-progress-success'
    ),
    \Xorinzor\Shoutzor\Model\Music::STATUS_ERROR => array(
        'label' => 'Error',
        'progressbar' => 'An error occurred while processing, please try again',
        'labelclass' => 'uk-badge-danger',
        'progressbarclass' => 'uk-progress-danger'
    ),
    \Xorinzor\Shoutzor\Model\Music::STATUS_DUPLICATE => array(
        'label' => 'Duplicate',
        'progressbar' => 'This song has already been uploaded',
        'labelclass' => 'uk-badge-danger',
        'progressbarclass' => 'uk-progress-danger'
    )
);

$uploadStatus = $status[$upload->status];
?>
<li data-uploadid="<?= $upload->id; ?>">
    <div class="uploaded-item">
        <p><div class="uk-badge <?= $uploadStatus['labelclass']; ?>"><?= $uploadStatus['label']; ?></div> <strong><?= $upload->title; ?></strong></p>

        <div class="uk-progress <?= $uploadStatus['progressbarclass']; ?>">
            <div class="uk-progress-bar" style="width: 100%;"><?= $uploadStatus['progressbar']; ?></div>
        </div>
    </div>
</li>