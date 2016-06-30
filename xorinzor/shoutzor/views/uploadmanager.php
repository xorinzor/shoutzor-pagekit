<?php $view->style('uikit-progress', 'theme:css/components/progress.almost-flat.min.css'); ?>
<?php $view->style('uikit-upload', 'theme:css/components/upload.almost-flat.min.css'); ?>
<?php $view->style('uikit-upload-formfile', 'theme:css/components/form-file.almost-flat.min.css'); ?>
<?php $view->style('uikit-placeholder', 'theme:css/components/placeholder.almost-flat.min.css'); ?>

<?php $view->script('uikit-upload-script', 'theme:js/components/upload.min.js', ['jquery', 'uikit-script']) ?>

<div id="upload-rules" class="uk-alert uk-alert-danger"><strong>Warning!</strong> Do NOT upload 18+ content or other harmful content, this will NOT be tolerated.</div>

<div id="upload-rules" class="uk-alert uk-alert-info"><strong>Notice</strong> The maximum file size is <?= $maxFileSize; ?></div>
<div id="upload-rules" class="uk-alert uk-alert-info"><strong>Notice</strong> The media duration limit is <?= $maxDuration; ?> minutes</div>

<div id="upload-drop" class="uk-placeholder uk-placeholder-large uk-text-center">
    <i class="uk-icon-cloud-upload uk-icon-medium uk-text-muted uk-margin-small-right"></i> Drop your file(s) here or <a class="uk-form-file">Select a file<input id="upload-select" type="file"></a>
</div>

<div id="progressbar" class="uk-progress uk-hidden">
    <div class="uk-progress-bar" style="width: 0%;">...</div>
</div>

<script type="text/javascript">
    $(function(){
        var progressbar     = $("#progressbar"),
            bar             = progressbar.find('.uk-progress-bar'),
            uploadList      = $("#uploadList"),
            uploadListEmpty = $("#uploadListEmpty"),
            settings        = {
                action: '<?= $view->url('@shoutzor/api/index'); ?>', // upload url
                single: true,
                param: 'musicfile',
                params: { method: "upload" },
                type: 'json',
                allow : '*.(wav|mp3|oga|flac|m4a|wma)', // allow only audio and video files

                notallowed: function(file, settings) {
                    //When an non-allowed file is beeing uploaded
                    $("#not-allowed").removeClass('uk-hidden');
                },

                loadstart: function() {
                    bar.css("width", "0%").text("0%");
                    progressbar.removeClass("uk-hidden");
                },

                progress: function(percent) {
                    percent = Math.ceil(percent);
                    bar.css("width", percent+"%").text(percent+"%");
                },

                complete: function(response, xhr) {

                    if(xhr.status != 200) {
                        //Something happened
                        $("#upload-error").removeClass("uk-hidden");
                        return;
                    }

                    if(response.info.code != 200) {
                        //Something happened in our API
                        $("#upload-error").removeClass("uk-hidden");
                        return;
                    }

                    //Hide the message telling us that the list is empty if we are adding one now
                    if(uploadList.find("li:not(#uploadListEmpty)").length == 0) {
                        uploadListEmpty.addClass("uk-hidden");
                    }

                    uploadList.prepend(
                        '<li data-uploadid="'+response.data.id+'">'+
                            '<div class="uploaded-item">'+
                                '<p><div class="uk-badge uk-badge-warning">Waiting</div> <strong>'+response.data.title+'</strong></p>'+
                                '<div class="uk-progress uk-progress-warning uk-progress-striped uk-active">'+
                                    '<div class="uk-progress-bar" style="width: 100%;">Waiting to be processed..</div>'+
                                '</div>'+
                            '</div>'+
                        '</li>');
                },

                allcomplete: function(response) {
                    bar.css("width", "100%").text("100%");

                    setTimeout(function(){
                        progressbar.addClass("uk-hidden");
                    }, 250);
                }
            };

        var select = UIkit.uploadSelect($("#upload-select"), settings),
            drop   = UIkit.uploadDrop($("#upload-drop"), settings);
    });
</script>

<div id="not-allowed" class="uk-alert uk-alert-danger uk-hidden"><strong>Not allowed!</strong> Allowed filetypes are: wav, mp3, oga, flac, m4a &amp; wma</div>
<div id="upload-error" class="uk-alert uk-alert-danger uk-hidden"><strong>Error!</strong> One or more files failed to upload, please try again.</div>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Upload progress</p>
    </div>

    <ul id="uploadList" class="uk-list uk-list-line">
        <?php if(count($uploads) == 0): ?>
            <li id="uploadListEmpty"><p>You have no remaining uploads (finished uploads will not show here)</p></li>
        <?php endif; ?>

        <?php foreach($uploads as $upload): ?>
            <?= $view->render('shoutzor:views/elements/uploadmanager-item.php', ['upload' => $upload]); ?>
        <?php endforeach; ?>
    </ul>
</div>
