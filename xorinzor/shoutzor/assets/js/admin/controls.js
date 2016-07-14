$(function() {

    var controlstatus = {
        toggleTimeout: 6000,
        status: null,

        executing: function() {
            new PNotify({
                title: 'Please wait',
                text: 'executing command',
                type: 'info',
                hide: true,
                delay: 1500
            });
        },

        warn: function() {
            new PNotify({
                title: 'Please wait',
                text: 'give the command a few seconds to complete',
                type: 'notice',
                hide: true,
                delay: 2000
            });
        },

        skip: function() {
            if(controlstatus.status == 'skipping') {
                controlstatus.warn();
                return;
            }

            controlstatus.status = 'skipping';
            controlstatus.executing();

            api.nexttrack(function(data) {
                new PNotify({
                    title: (data.result) ? 'Command accepted' : 'Command failed',
                    text: (data.result) ? 'Shoutzor has skipped to the next track' : data.message,
                    type: (data.result) ? 'success' : 'error',
                    hide: true,
                    delay: 1500
                });

                setTimeout(function() {
                    controlstatus.status = null;
                }, controlstatus.toggleTimeout);
            });
        }
    };

    $("#skipTrack").on("click", function() {
        controlstatus.skip();
    });
});
