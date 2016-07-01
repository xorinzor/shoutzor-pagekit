<?php
    use Xorinzor\Shoutzor\App\Utility;
?>

<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
    <thead>
        <tr>
            <th>Title</th>
            <th>Artist(s)</th>
            <th>Album(s)</th>
            <th>Duration</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($tracks as $track): ?>
            <tr>
                <td><?= $track->title; ?></td>
                <td>
                    <?php
                        if(isset($track->artist) && !is_null($track->artist) && count($track->artist) > 0) {
                            $artistList = '';

                            foreach($track->artist as $artist) {
                                if(!empty($artistList)) {
                                    $artistList .= ', ';
                                }

                                $artistList .= '<a href="' . $view->url('@shoutzor/artist/view', ['id' => $artist->id]) . '">' . $artist->name . '</a>';
                            }

                            echo $artistList;
                        } else {
                            echo __('Unknown');
                        }
                    ?>
                </td>
                <td>
                    <?php
                        if(isset($track->album) && !is_null($track->album) && count($track->album) > 0) {
                            $albumList = '';

                            foreach($track->album as $album) {
                                if(!empty($albumList)) {
                                    $albumList .= ', ';
                                }

                                $albumList .= '<a href="' . $view->url('@shoutzor/album/view', ['id' => $album->id]) . '">' . $album->title . '</a>';
                            }

                            echo $albumList;
                        } else {
                            echo __('Unknown');
                        }
                    ?>
                </td>
                <td><?= Utility::secondsToTime($track->duration); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
