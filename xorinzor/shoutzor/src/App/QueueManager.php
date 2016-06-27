<?php

namespace Xorinzor\Shoutzor\App;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Media;
use Xorinzor\Shoutzor\Model\Request;
use Xorinzor\Shoutzor\App\Liquidsoap\LiquidsoapManager;

class QueueManager {

    public function getQueueList() {
        return App::db()->createQueryBuilder()->select('*')->from('@shoutzor_requestlist')->related('media')->execute()->fetchAll();
    }

    public function getNextFromQueue() {
        return App::db()->createQueryBuilder()->select('*')->from('@shoutzor_requestlist')->orderBy('requesttime', 'ASC')->related('media')->first();
    }

    public function removeNextFromQueue() {
        return App::db()->createQueryBuilder()->select('*')->from('@shoutzor_requestlist')->orderBy('requesttime', 'ASC')->delete();
    }

    public function getQueueCount() {
        return App::db()->createQueryBuilder()->select('*')->from('@shoutzor_requestlist')->count();
    }

    public function addToQueue(Media $media, $createRequest = true) {
        //Get the config options
        $config = App::module('shoutzor')->config('shoutzor');

        //Get the path to the file
        $filepath = $config['mediaDir'] . '/' . $media->filename;

        //Make sure the file is readable
        if (!is_readable($filepath)) {
            throw new Exception(__('Cannot read music file '.$filepath.', Permission denied.'));
        }

        //Add request to the playlist
        $liquidsoapManager = new LiquidsoapManager();
        $liquidsoapManager->queueTrack($filepath);

        if($createRequest === true) {
            //Save request in the database
            $request = Request::create();
            $request->save(array(
                'media_id' => $media->id,
                'requester_id' => App::user()->id,
                'requesttime' => (new \DateTime())->format('Y-m-d H:i:s')
            ));
        }
        
        return true;
    }

}
