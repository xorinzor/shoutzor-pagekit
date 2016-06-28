<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Media;
use Xorinzor\Shoutzor\Model\History;

class SiteController
{

    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        $config = App::module('shoutzor')->config('liquidsoap');

        $uploaded = Media::where(['status = :finished'], ['finished' => Media::STATUS_FINISHED])->orderBy('created', 'DESC')->related(['artist', 'user'])->limit(8)->get();
        $history = History::query()->orderBy('id', 'DESC')->limit(5)->related('media')->get();

        return [
            '$view' => [
                'title' => __('Dashboard'),
                'name' => 'shoutzor:views/index.php'
            ],
            'uploaded' => $uploaded,
            'history' => $history,
            'm3uFile' => 'http://'.$_SERVER['SERVER_NAME'] . ':8000' . $config['wrapperOutputMount'] . '.m3u'
        ];
    }

    /**
     * @Route("/uploadmanager", name="uploadmanager", methods="GET")
     */
    public function uploadManagerAction()
    {

        $uploads = Media::where(['uploader_id = :uploader AND status != :finished'], ['uploader' => App::user()->id, 'finished' => Media::STATUS_FINISHED])->orderBy('created', 'DESC')->related(['artist', 'user'])->get();

        return [
            '$view' => [
                'title' => __('Upload Manager'),
                'name' => 'shoutzor:views/uploadmanager.php'
            ],
            'uploads' => $uploads,
            'maxFileSize' => $this->formatBytes($this->file_upload_max_size())
        ];
    }

    /**
     * @Route("/search", name="search", methods="GET")
     * @Request({"q":"string"})
     */
    public function searchAction($q = "")
    {
        if(empty($q)) {
            return [
                '$view' => [
                    'title' => 'Search',
                    'name' => 'shoutzor:views/search_error.php'
                ]
            ];
        }
        $query = Media::where(['status = :finished AND (title LIKE :search OR filename LIKE :search)'], ['finished' => Media::STATUS_FINISHED, 'search' => "%{$q}%"])
                ->orderBy('created', 'DESC');

        $total = $query->count();

        $results = $query->related(['artist'])
                ->limit(10)
                ->get();

        return [
            '$view' => [
                'title' => 'Search',
                'name' => 'shoutzor:views/search.php'
            ],

            'searchterm' => htmlspecialchars($q),
            'total' => $total,
            'results' => $results
        ];
    }

    public function redirectAction()
    {
        return App::response()->redirect('@shoutzor/greet', ['name' => 'Someone']);
    }

    public function jsonAction()
    {
        return ['message' => 'There is nothing here. Move along.'];
    }

    public function downloadAction()
    {
        return App::response()->download('extensions/shoutzor/extension.svg');
    }

    function forbiddenAction()
    {
        App::abort(401, __('Permission denied.'));
    }

    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    // Returns a file size limit in bytes based on the PHP upload_max_filesize
    // and post_max_size
    private function file_upload_max_size() {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $max_size = $this->parse_size(ini_get('post_max_size'));

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = $this->parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    private function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }
}
