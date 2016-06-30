<?php

namespace Xorinzor\Shoutzor\App;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Media;
use Xorinzor\Shoutzor\Model\Request;
use Xorinzor\Shoutzor\Model\History;
use Xorinzor\Shoutzor\App\QueueManager;

use DateTime;
use DateInterval;

class AutoDJ {

    private $queueManager;

    public function __construct() {
        $this->queueManager = new QueueManager();
    }

    public function importQueue() {
        if($this->queueManager->getQueueCount() > 0) {
            $queue = $this->queueManager->getQueueList();
            foreach($queue as $request) {
                $this->queueManager->addToQueue($request->media, false);
            }
        }

        $this->checkQueue();
    }

    public function playNext() {
        //Add the current item to the history
        $item = $this->queueManager->getNextFromQueue();
        if($item !== null) {
            $this->queueManager->addToHistory($item->media, $item->requester_id);
        }

        //Remove the next first item from the queue
        $this->queueManager->removeNextFromQueue();

        //Make sure there are enough items left in the queue, if not, we have to step in and add some
        $this->checkQueue();
    }

    private function checkQueue() {

        $queueCount = $this->queueManager->getQueueCount();

        //Shoutzor needs at least 2 songs for crossfading, currently 0 thus add 2
        if($queueCount == 0) {
            $this->queueManager->addToQueue($this->getRandomTrack());
            $this->queueManager->addToQueue($this->getRandomTrack());
        }
        //Shoutzor needs at least 2 songs for crossfading, currently 1 thus add 1
        elseif($queueCount == 1) {
            $this->queueManager->addToQueue($this->getRandomTrack());
        }
        //Shoutzor needs at least 2 songs for crossfading, currently 2 so we're good.
        else {
            //We dont have to do anything
        }
    }

    public function getRandomTrack($autoForce = true, $forced = false) {
        if($forced === false) {
            $config = App::module('shoutzor')->config('shoutzor');

            $requestHistoryTime = (new DateTime())->sub(new DateInterval('PT'.$config['mediaRequestDelay'].'M'))->format('Y-m-d H:i:s');

            //Get a list of all recently played media files
            $listRecent = History::query()->select('media_id')->where('played_at > :maxTime', ['maxTime' => $requestHistoryTime])->execute()->fetchAll();

            //$test = array_unique($listRecent);

            //Get the ID's from each recently played media file
            $listRecent = array_map(function($e) {
                return is_object($e) ? $e->media_id : $e['media_id'];
            }, $listRecent);

            //Make sure only unique values are in the array
            $listRecent = array_unique($listRecent);

            //Get a list of all queued media files
            $listQueued = Request::query()->select('media_id')->execute()->fetchAll();

            //Get the ID's from each queued media file
            $listQueued = array_map(function($e) {
                return is_object($e) ? $e->media_id : $e['media_id'];
            }, $listQueued);

            //Make sure only unique values are in the array
            $listQueued = array_unique($listQueued);

            //Merge the 2 lists of ID's which should not be picked anymore
            $list = array_merge($listRecent, $listQueued);
        } else {
            $list = array();
        }

        $song = Media::query()->whereInSet('id', $list, true)->where('status = :status', ['status' => Media::STATUS_FINISHED])->orderBy('rand()');

        if($song->count() === 0) {
            return ($autoForce === true) ? $this->getRandomTrack(true, true) : false;
        }

        return $song->first();
    }

}
