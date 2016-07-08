$(function() {

    var shoutzorstatus = {
        toggleTimeout: 3000,

        status: {
            wrapper: null,
            shoutzor: null
        },

        isStarted: function(type) {
            $("#" + type + "Toggle").data('status', 'started');
            $("#" + type + "Toggle").removeClass('uk-button-success');
            $("#" + type + "Toggle").addClass('uk-button-danger');
            $("#" + type + "Toggle").html('Activate ' + type);
        },

        isStopped: function(type) {
            $("#" + type + "Toggle").data('status', 'stopped');
            $("#" + type + "Toggle").removeClass('uk-button-danger');
            $("#" + type + "Toggle").addClass('uk-button-success');
            $("#" + type + "Toggle").html('Activate ' + type);
        },

        executing: function() {
            new PNotify({
                title: 'Please wait',
                text: 'executing command',
                type: 'info',
                hide: true,
                delay: 2000
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

        start: function(type) {
            if(typeof type === 'string') {
                type = type.toLowerCase();
            } else {
                return;
            }

            if(shoutzorstatus.status[type] == 'starting') {
                shoutzorstatus.warn();
                return;
            }

            shoutzorstatus.status[type] = 'starting';
            shoutzorstatus.executing();

            api.startscript(type, function(data) {
                new PNotify({
                    title: (data.result) ? 'Command accepted' : 'Command failed',
                    text: (data.result) ? 'The script "' + type + '" is starting' : data.message,
                    type: (data.result) ? 'success' : 'error',
                    hide: true,
                    delay: 2000
                });

                if(data.result) {
                    shoutzorstatus.isStarted(type);
                }

                setTimeout(function() {
                    shoutzorstatus.status[type] = null;
                }, shoutzorstatus.toggleTimeout);
            });
        },

        stop: function(type) {
            if(typeof type === 'string') {
                type = type.toLowerCase();
            } else {
                return;
            }

            if(shoutzorstatus.status[type] == 'stopping') {
                shoutzorstatus.warn();
                return;
            }

            shoutzorstatus.status[type] = 'stopping';
            shoutzorstatus.executing();

            api.stopscript(type, function(data) {
                new PNotify({
                    title: (data.result) ? 'Command accepted' : 'Command failed',
                    text: (data.result) ? 'The script "' + type + '" is stopping' : data.message,
                    type: (data.result) ? 'success' : 'error',
                    hide: true,
                    delay: 2000
                });

                if(data.result) {
                    shoutzorstatus.isStopped(type);
                }

                setTimeout(function() {
                    shoutzorstatus.status[type] = null;
                }, shoutzorstatus.toggleTimeout);
            });
        }
    };

    //Bind #wrapperToggle button on-click function
    $("#wrapperToggle").on("click", function() {
        status = $(this).data("status");
        if(typeof status === 'string') {
            status = status.toLowerCase();
        } else {
            return;
        }

        if(status == 'started') {
            shoutzorstatus.stop('wrapper');
        } else {
            shoutzorstatus.start('wrapper');
        }
    });

    //Bind #shoutzorToggle button on-click function
    $("#shoutzorToggle").on("click", function() {
        status = $(this).data("status");
        if(typeof status === 'string') {
            status = status.toLowerCase();
        } else {
            return;
        }

        if(status == 'started') {
            shoutzorstatus.stop('shoutzor');
        } else {
            shoutzorstatus.start('shoutzor');
        }
    });
});
