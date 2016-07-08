$(function() {

    var shoutzorstatus = {
        toggleTimeout: 3000,

        status: {
            wrapper: null,
            shoutzor: null
        },

        warn: function() {
            new PNotify({
                title: 'Please wait',
                text: 'give the command a few seconds to complete',
                type: 'notice',
                hide: true,
                delay: 1500
            });
        },

        start: function(type) {
            type = type.toLowerCase();

            if(shoutzorstatus.status[type] == 'starting') {
                shoutzorstatus.warn();
                return;
            }

            shoutzorstatus.status[type] = 'starting';

            api.startscript(type, function(data) {
                new PNotify({
                    title: (data.result) ? 'Command accepted' : 'Command failed',
                    text: (data.result) ? 'The script "' + type + '" is starting' : data.message,
                    type: (data.result) ? 'success' : 'error',
                    hide: true,
                    delay: 1500
                });

                setTimeout(function() {
                    shoutzorstatus.status[type] = null;
                }, shoutzorstatus.toggleTimeout);
            });
        },

        stop: function(type) {
            type = type.toLowerCase();

            if(shoutzorstatus.status[type] == 'stopping') {
                shoutzorstatus.warn();
                return;
            }

            shoutzorstatus.status[type] = 'stopping';

            api.stopscript(type, function(data) {
                new PNotify({
                    title: (data.result) ? 'Command accepted' : 'Command failed',
                    text: (data.result) ? 'The script "' + type + '" is stopping' : data.message,
                    type: (data.result) ? 'success' : 'error',
                    hide: true,
                    delay: 1500
                });

                setTimeout(function() {
                    shoutzorstatus.status[type] = null;
                }, shoutzorstatus.toggleTimeout);
            });
        }
    };

    //Bind #wrapperToggle button on-click function
    $("#wrapperToggle").on("click", function() {
        status = $(this).data("status").toLowerCase();

        if(status == 'started') {
            shoutzorstatus.stop('wrapper');
        } else {
            shoutzorstatus.start('wrapper');
        }
    });

    //Bind #shoutzorToggle button on-click function
    $("#shoutzorToggle").on("click", function() {
        status = $(this).data("status").toLowerCase();

        if(status == 'started') {
            shoutzorstatus.stop('shoutzor');
        } else {
            shoutzorstatus.start('shoutzor');
        }
    });
});
