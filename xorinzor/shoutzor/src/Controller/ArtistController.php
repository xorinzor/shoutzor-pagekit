<?php
namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

use Xorinzor\Shoutzor\Model\Artist;
use Xorinzor\Shoutzor\Model\Album;
use Xorinzor\Shoutzor\Model\Media;

class ArtistController
{
    /**
     * @Route("/", name="index")
     * @Request({"page": "int"})
     */
    public function indexAction($page = 1)
    {
        $query = Artist::query()->select('*');

        $limit = 10;
        $count = $query->count();
        $total = ceil($count / $limit);
        $page  = max(1, min($total, $page));

        $artists = $query->offset(($page-1) * $limit)->limit($limit)->orderBy('name', 'ASC')->get();

        $request = App::request();

        return [
            '$view' => [
                'title' => 'Artists',
                'name'  => 'shoutzor:views/artist/index.php'
            ],
            'artists'    => $artists,
            'page'      => $page,
            'total'     => $total
        ];
    }

    /**
     * @Route("/{id}", name="view", requirements={"id"="\d+"})
     */
    public function viewAction($id)
    {
        $artist = Artist::query()->where('id = ?', [$id])->related('album')->first();
        $request = App::request();

        if(is_null($artist))
        {
            $request->getSession()->getFlashBag()->add('error', __('Tried to view an non-existing Artist page'));
            return App::response()->redirect('@shoutzor/artist/index');
        }

        $topTracks = $artist->getTopTracks();

        return [
            '$view' => [
                'title' => 'Artist: ' . $artist->name,
                'name'  => 'shoutzor:views/artist/view.php',
            ],
            'image' => (is_null($artist->image)) ? App::url()->getStatic('shoutzor:assets/images/profile-placeholder.png') : App::url()->getStatic('shoutzor:' . App::module('shoutzor')->config('shoutzor')['imageDir'] . '/' . $artist->image),
            'summary' => empty($artist->summary) ? __('No summary for this artist is available') : $artist->summary,
            'artist' => $artist,
            'topTracks' => $topTracks,
            'albums' => $artist->getAlbums()
        ];
    }
}
