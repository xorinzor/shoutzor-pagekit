<?php $view->style('shoutzor-style-main', 'shoutzor:assets/css/style.css') ?>

<?php $view->style('uikit-progress', 'theme:css/components/progress.almost-flat.min.css'); ?>
<?php $view->style('uikit-upload', 'theme:css/components/upload.almost-flat.min.css'); ?>
<?php $view->style('uikit-placeholder', 'theme:css/components/placeholder.almost-flat.min.css'); ?>

<?php $view->script('uikit-upload-script', 'theme:js/components/upload.min.js', ['jquery', 'uikit-script']) ?>

<div id="upload-drop" class="uk-placeholder uk-text-center">
    <i class="uk-icon-cloud-upload uk-icon-medium uk-text-muted uk-margin-small-right"></i> Drop your file here or <a class="uk-form-file">Select a file<input class="uk-hidden" id="upload-select" type="file"></a>
</div>

<script>

    $(function(){

        var settings    = {

                action: '/shoutzor/upload', // upload url

                allow : '*.(mp3)', // allow only audio and video files

                loadstart: function() {
                    //Run this when the upload plugin has loaded
                },

                progress: function(percent) {
            //        percent = Math.ceil(percent);
            //        bar.css("width", percent+"%").text(percent+"%");
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
        <li>
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
        </li>
    </ul>
</div>