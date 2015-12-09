<?php $view->style('shoutzor-style-main', 'shoutzor:assets/css/style.css') ?>

<?php $view->style('uikit-progress', 'theme:css/components/progress.almost-flat.min.css'); ?>
<?php $view->style('uikit-upload', 'theme:css/components/upload.almost-flat.min.css'); ?>
<?php $view->style('uikit-upload-formfile', 'theme:css/components/form-file.almost-flat.min.css'); ?>
<?php $view->style('uikit-placeholder', 'theme:css/components/placeholder.almost-flat.min.css'); ?>

<?php $view->script('uikit-upload-script', 'theme:js/components/upload.min.js', ['jquery', 'uikit-script']) ?>

<div id="upload-drop" class="uk-placeholder uk-placeholder-large uk-text-center">
    <i class="uk-icon-cloud-upload uk-icon-medium uk-text-muted uk-margin-small-right"></i> Drop your file(s) here or <a class="uk-form-file">Select a file<input id="upload-select" type="file"></a>
</div>

<script type="text/javascript">
    $(function(){
        var settings    = {
                action: '<?= $view->url('@shoutzor/uploadmanager'); ?>', // upload url
                single: true,
                params: {},
                type: 'json',
                allow : '*.(mp3|mkv|avi|flv)', // allow only audio and video files

                notallowed: function(file, settings) {
                    //When an non-allowed file is beeing uploaded
                },

                loadstart: function() {
                    //Run this when the upload plugin has loaded
                },

                progress: function(percent) {
                    //percent = Math.ceil(percent);
                    //bar.css("width", percent+"%").text(percent+"%");
                },

                complete: function(reponse, xhr) {

                },

                allcomplete: function(response) {
                    /*bar.css("width", "100%").text("100%");

                    setTimeout(function(){
                        progressbar.addClass("uk-hidden");
                    }, 250);

                    alert("Upload Completed")*/
                }
            };

        var select = UIkit.uploadSelect($("#upload-select"), settings),
            drop   = UIkit.uploadDrop($("#upload-drop"), settings);
    });
</script>

<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Upload progress</p>
    </div>

    <ul class="uk-list uk-list-line">
        <li><p>You have no remaining uploads</p></li>

        <?php foreach($uploads as $upload): ?>
        <?php endforeach; ?>

<!--        <li>
            <div class="uploaded-item">
                <p><strong>Ghosts 'n Stuff</strong> - Deadmau5</p>

                <div class="uk-progress">
                    <div class="uk-progress-bar" style="width: 47%;">Uploading.. 47%</div>
                </div>
            </div>
        </li>
        <li>
            <div class="uploaded-item">
                <p><div class="uk-badge uk-badge-warning">Waiting</div> <strong>Ghosts 'n Stuff</strong> - Deadmau5</p>

                <div class="uk-progress uk-progress-warning uk-progress-striped uk-active">
                    <div class="uk-progress-bar" style="width: 100%;">Waiting to be processed..</div>
                </div>
            </div>
        </li>
        <li>
            <div class="uploaded-item">
                <p><div class="uk-badge">Processing</div> <strong>Ghosts 'n Stuff</strong> - Deadmau5</p>

                <div class="uk-progress uk-progress-striped uk-active">
                    <div class="uk-progress-bar" style="width: 100%;">Processing..</div>
                </div>
            </div>
        </li>
        <li>
            <div class="uploaded-item">
                <p><div class="uk-badge uk-badge-success">Finished</div> <strong>Ghosts 'n Stuff</strong> - Deadmau5</p>

                <div class="uk-progress uk-progress-success">
                    <div class="uk-progress-bar" style="width: 100%;">Finished</div>
                </div>
            </div>
        </li>
        <li>
            <div class="uploaded-item">
                <p><div class="uk-badge uk-badge-danger">Error</div> <strong>Ghosts 'n Stuff</strong> - Deadmau5</p>

                <div class="uk-progress uk-progress-danger">
                    <div class="uk-progress-bar" style="width: 100%;">An error occured while processing, please try again</div>
                </div>
            </div>
        </li>
        <li>
            <div class="uploaded-item">
                <p><div class="uk-badge uk-badge-danger">Exists</div> <strong>Ghosts 'n Stuff</strong> - Deadmau5</p>

                <div class="uk-progress uk-progress-danger">
                    <div class="uk-progress-bar" style="width: 100%;">This song has already been uploaded</div>
                </div>
            </div>
        </li>-->
    </ul>
</div>