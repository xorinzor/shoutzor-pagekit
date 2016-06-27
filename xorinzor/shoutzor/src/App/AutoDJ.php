<?php

namespace Xorinzor\Shoutzor\App;

use Xorinzor\Shoutzor\Model\Media;
use Xorinzor\Shoutzor\App\QueueManager;

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

    private function getRandomTrack($forced = false) {
        return Media::where('1=1', [])->orderBy('rand()')->first();
    }

}
