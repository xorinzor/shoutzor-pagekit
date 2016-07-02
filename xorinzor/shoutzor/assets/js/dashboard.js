$(function() {

    var dashboard = {

        rowTemplate: $.templates("#dashboard-table-row-template"),

        buildArtistList: function(artists) {
            var list = '';

            if(artists.length === 0) {
                return 'Unknown';
            }

            $.each(artists, function(key, artist) {
                if(list !== '') {
                    list += ', ';
                }

                list += '<a href="' + artist.url + '">' + artist.name + '</a>';
            });

            return list;
        },

        buildAlbumList: function(albums) {
            var list = '';

            if(albums.length === 0) {
                return 'Unknown';
            }

            $.each(albums, function(key, album) {
                if(list !== '') {
                    list += ', ';
                }

                list += '<a href="' + album.url + '">' + album.title + '</a>';
            });

            return list;
        },

        buildQueueTableRows: function(tracks, starttime) {
            var result = '';

            starttime = starttime * 1000; //Convert to milliseconds

            $.each(tracks, function(key, track) {
                result += dashboard.rowTemplate.render({
                    title: track.title,
                    artist: dashboard.buildArtistList(track.artist),
                    album: dashboard.buildAlbumList(track.album),
                    duration: $.format.date((new Date(track.duration * 1000 + starttime)).toISOString(), 'yyyy-MM-dd HH:mm:ss')
                });

                starttime = track.duration * 1000 + starttime;
            });

            return result;
        },

        buildHistoryTableRows: function(tracks) {
            var result = '';

            $.each(tracks, function(key, track) {
                result += dashboard.rowTemplate.render({
                    title: track.title,
                    artist: dashboard.buildArtistList(track.artist),
                    album: dashboard.buildAlbumList(track.album),
                    duration: track.played_at
                });
            });

            return result;
        },

        updateQueueList: function() {
            api.queuelist(function(result) {
                if(result.result === false) {
                    //Error handling
                } else {
                    //Clear the current list
                    $("#queue-table tbody").html(dashboard.buildQueueTableRows(result.tracks, starttime));
                }
            });
        },

        updateHistoryList: function() {
            api.historylist(function(result) {
                if(result.result === false) {
                    //Error handling
                } else {
                    //Clear the current list
                    $("#history-table tbody").html(dashboard.buildHistoryTableRows(result.tracks));
                }
            });
        },

        autoUpdate: function() {
            dashboard.updateQueueList();
            dashboard.updateHistoryList();

            setTimeout(function() {
                dashboard.autoUpdate();
            }, 10000);
        }
    };

    dashboard.autoUpdate();
});
