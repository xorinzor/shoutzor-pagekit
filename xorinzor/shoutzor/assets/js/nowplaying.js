$(function(){
    var url = 'http://' + window.location.hostname + ":8000/json.xsl?callback=?";
    var offline = "Shoutzor is currently offline";
    var song = "title - artist";

    function nowplaying(data) {
        if(data === false) {
            $("#nowplaying").html(offline);
            return;
        }

        try {
            var songData = {
               title:data.mounts["/shoutzor"].title,
               artist:data.mounts["/shoutzor"].artist
            };

            result = song.replace(/title|artist/gi, function(matched){
              return songData[matched];
            });

        } catch(e) {
            result = offline;
        }

        $("#nowplaying").html(result);
    }

    function fetchInfo() {
        $.ajax({
            url: url,
            dataType: "jsonp",
            jsonpCallback: "nowplaying",
            method: "GET",
            error: function(e) {
               nowplaying(false);
           },
           success: function(data) {
               nowplaying(data);
           }
        });

        setTimeout(fetchInfo, 3000);
    }

    fetchInfo();
});
