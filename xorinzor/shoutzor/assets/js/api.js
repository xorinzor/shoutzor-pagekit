var api = {
    url: "/shoutzorapi",

    executeRequest: function(request) {
        return $.ajax({
            url: request.url,
            type: request.type,
            data: request.data,
            success: request.successCallback,
            error: request.errorCallback
        });
    },

    upload: function() {
        
    }
}
