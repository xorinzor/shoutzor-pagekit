var api = {
    url: "/shoutzorapi",

    executeRequest: function(request) {
        request = $.extend({
            url: api.url,
            type: "GET",
            data: {}
        }, request);

        return $.ajax({
            url: request.url,
            type: request.type,
            data: request.data
        });
    },

    //Request a media file to be played
    request: function(trackid, callback) {
        api.executeRequest({
            data: {
                method: "request",
                id: trackid
            }
        }).always(function(data, type) {
            var result = false;
            var message = '';

            //make sure the request did not fail
            if (type == "success") {
                //API call went through, make sure the call succeeded
                if(data.info.code == 200) {
                    result = true;
                    message = 'Your request has been added';
                } else {
                    message = data.data
                }
            } else {
                //Some error happened when trying to make the API call (500 perhaps?)
                message = 'Sorry! Something went wrong!';
            }

            callback({
                result: result,
                message: message
            });
        });
    }
}
