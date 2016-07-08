$(document).ready(function() {
    /**
     * This gets called whenever an element is clicked thats used for requesting a media object
     */
    $("figcaption[data-music], a[data-music]").on('click', function() {
        var clickedItem = $(this);

        //Create a confirmation dialog to make sure the user intended to request this media object
        (new PNotify({
            title: 'Request media',
            text: 'Do you want to request this song?',
            icon: 'glyphicon glyphicon-question-sign',
            hide: true,
            delay: 1500,
            confirm: {
                confirm: true
            },
            addclass: 'stack-modal',
            stack: {'dir1': 'down', 'dir2': 'right', 'modal': true}
        })).get().on('pnotify.confirm', function(){
            //Remove the overlay
            $(".ui-pnotify-modal-overlay").remove();

            //User wants to request this song, making the call
            api.request(clickedItem.data("music"), function(data) {
                new PNotify({
                    title: (data.result) ? 'Request success' : 'Request failed',
                    text: (data.result) ? 'Your request has been added' : data.message,
                    type: (data.result) ? 'success' : 'error',
                    hide: true,
                    delay: 1500
                });
            });
        }).on('pnotify.cancel', function(){
            //Remove the overlay
            $(".ui-pnotify-modal-overlay").remove();
        });
    });


}); //end of document.ready
