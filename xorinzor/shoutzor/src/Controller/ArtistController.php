<?php
namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

use Xorinzor\Shoutzor\Model\Artist;

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
        $artist = Artist::find($id);
        $request = App::request();

        if(is_null($artist))
        {
            $request->getSession()->getFlashBag()->add('error', __('Tried to view an non-existing Artist page'));
            return App::response()->redirect('@shoutzor/artist/index');
        }


        return [
            '$view' => [
                'title' => 'Artist: ' . $artist->name,
                'name'  => 'shoutzor:views/artist/view.php',
            ],
            'artist' => $artist,
        ];
    }
}
