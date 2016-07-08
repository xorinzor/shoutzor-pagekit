<?php $view->style('uikit-progress', 'theme:css/components/progress.almost-flat.min.css'); ?>
<?php $view->style('uikit-upload', 'theme:css/components/upload.almost-flat.min.css'); ?>
<?php $view->style('uikit-upload-formfile', 'theme:css/components/form-file.almost-flat.min.css'); ?>
<?php $view->style('uikit-placeholder', 'theme:css/components/placeholder.almost-flat.min.css'); ?>

<?php $view->script('jsrender', 'shoutzor:assets/js/jsrender.js', 'jquery') ?>
<?php $view->script('uikit-upload-script', 'theme:js/components/upload.min.js', ['jquery', 'uikit-script']) ?>
<?php $view->script('uploadmanager', 'shoutzor:assets/js/uploadmanager.js', ['uikit-upload-script', 'jsrender']) ?>

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
            isUploading     = false,
            itemsLeft       = 0,
            settings        = {
                action: '<?= $view->url('@shoutzor/api/index'); ?>', // upload url
                single: true,
                param: 'musicfile',
                params: { method: "upload" },
                type: 'json',
                allow : '*.(wav|mp3|oga|flac|m4a|wma)', // allow only audio and video files

                beforeAll: function(files) {
                    itemsLeft = files.length;
                },

                before: function(settings, file) {
                    itemsLeft -= 1;
                    if(itemsLeft < 0) {
                        itemsLeft = 0;
                    }
                },

                notallowed: function(file, settings) {
                    //When an non-allowed file is beeing uploaded
                    $("#not-allowed").removeClass('uk-hidden');
                },

                loadstart: function() {
                    isUploading = true;
                    bar.css("width", "0%").text("0%");
                    progressbar.removeClass("uk-hidden");
                },

                progress: function(percent) {
                    percent = Math.ceil(percent);

                    if(percent == 100) {
                        bar.css("width", "100%").text("100% Uploading complete - Please wait while the upload is processed.. | " + itemsLeft + " uploads remaining");
                    } else {
                        bar.css("width", percent+"%").text(percent+"% | " + itemsLeft + " uploads remaining");
                    }
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

                    uploadmanager.addItem(response.data);
                },

                allcomplete: function(response) {
                    isUploading = false;

                    bar.css("width", "100%").text("100%");

                    setTimeout(function(){
                        progressbar.addClass("uk-hidden");
                    }, 250);
                }
            };

        var select = UIkit.uploadSelect($("#upload-select"), settings),
            drop   = UIkit.uploadDrop($("#upload-drop"), settings);

        //Make sure users dont accidentally leave the page while uploads are still running
        window.onbeforeunload = function(e) {
            if(isUploading) {
                return 'You have still uploads running, if you leave this page these will be canceled. Are you sure you want to leave?';
            }
        };
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

<script id="uploaded-item-template" type="text/x-jsrender">
    <li data-uploadid="{{:id}}">
        <div class="uploaded-item">
            <p><div class="uk-badge {{:labelclass}}">{{:label}}</div> <strong>{{:title}}</strong></p>
            <div class="uk-progress {{:progressbarclass}}">
                <div class="uk-progress-bar" style="width: 100%;">{{:progressbartext}}</div>
            </div>
        </div>
    </li>
</script>
