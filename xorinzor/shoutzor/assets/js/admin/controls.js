$(function() {

    $("#skipTrack").on("click", function() {
        api.nexttrack(function(data) {
            new PNotify({
                title: (data.result) ? 'Command accepted' : 'Command failed',
                text: (data.result) ? 'Shoutzor has skipped to the next track' : data.message,
                type: (data.result) ? 'success' : 'error',
                hide: true,
                delay: 1500
            });
        });
    });

});
