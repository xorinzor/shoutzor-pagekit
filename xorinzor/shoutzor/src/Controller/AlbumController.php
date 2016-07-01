<?php
namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

use Xorinzor\Shoutzor\Model\Artist;
use Xorinzor\Shoutzor\Model\Album;
use Xorinzor\Shoutzor\Model\Media;

class AlbumController
{
    /**
     * @Route("/", name="index")
     * @Request({"page": "int"})
     */
    public function indexAction($page = 1)
    {
        $query = Album::query()->select('*');

        $limit = 10;
        $count = $query->count();
        $total = ceil($count / $limit);
        $page  = max(1, min($total, $page));

        $albums = $query->offset(($page-1) * $limit)->limit($limit)->orderBy('title', 'ASC')->get();

        $request = App::request();

        return [
            '$view' => [
                'title' => 'Albums',
                'name'  => 'shoutzor:views/album/index.php'
            ],
            'albums'    => $albums,
            'page'      => $page,
            'total'     => $total
        ];
    }

    /**
     * @Route("/{id}", name="view", requirements={"id"="\d+"})
     */
    public function viewAction($id)
    {
        $album = Album::query()->where('id = ?', [$id])->related(['artist','media'])->first();
        $request = App::request();

        if(is_null($album))
        {
            $request->getSession()->getFlashBag()->add('error', __('Tried to view an non-existing Album'));
            return App::response()->redirect('@shoutzor/album/index');
        }

        $tracks = $album->getMedia();

        return [
            '$view' => [
                'title' => 'Album: ' . $album->title,
                'name'  => 'shoutzor:views/album/view.php',
            ],
            'image' => (is_null($album->image) || empty($album->image)) ? App::url()->getStatic('shoutzor:assets/images/album-placeholder.png') : App::url()->getStatic('shoutzor:' . App::module('shoutzor')->config('shoutzor')['imageDir'] . '/' . $album->image),
            'summary' => empty($album->summary) ? __('No summary for this album is available') : $album->summary,
            'album' => $album,
            'tracks' => $tracks
        ];
    }
}
