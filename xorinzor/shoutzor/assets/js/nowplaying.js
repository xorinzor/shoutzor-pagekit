$(function(){
    var url = 'http://' + window.location.hostname + ":8000/json.xsl?callback=?";
    var offline = "Shoutzor is currently offline";
    var song = "title - artist";

    function buildArtistList(artists) {
        var list = '';

        if(artists.length === 0) {
            return 'Unknown';
        }

        $.each(artists, function(key, artist) {
            if(list !== '') {
                list += ', ';
            }

            list += artist.name;
        });

        return list;
    }

    function nowplaying(track) {
        if(track === false) {
            $("#nowplaying").html(offline);
            return;
        }

        try {
            result = track.title + ' - ' + buildArtistList(track.artist);
        } catch(e) {
            result = offline;
        }

        $("#nowplaying").html(result);
    }

    function fetchInfo() {
        api.nowplaying(function(result){
            if(result.result === true) {
                nowplaying(result.track);
            } else {
                nowplaying(false);
            }
        });

        setTimeout(fetchInfo, 3000);
    }

    fetchInfo();
});
