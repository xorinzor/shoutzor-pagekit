<?php

namespace Pagekit\Blog\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Music;

/**
 * @Route("music", name="music")
 */
class MusicApiController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction($filter = [], $page = 0)
    {
        $query  = Music::query();
        $filter = array_merge(array_fill_keys(['search', 'is_video', 'order', 'limit'], ''), $filter);

        extract($filter, EXTR_SKIP);

        $query->where(['status' => Music::STATUS_FINISHED]);

        if($is_video) {
            $query->where(['is_video' => 1]);
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere(['title LIKE :search', 'filename LIKE :search'], ['search' => "%{$search}%"]);
            });
        }

        if (!preg_match('/^(date|title|comment_count)\s(asc|desc)$/i', $order, $order)) {
            $order = [1 => 'date', 2 => 'desc'];
        }

        $limit = (int) $limit ?: App::module('shoutzor')->config('search.results_per_page');

        //Make sure limit doesn't exceed our maximum (let's not overload the database)
        if($limit > App::module('shoutzor')->config('search.max_results_per_page')) {
            $limit = App::module('shoutzor')->config('search.max_results_per_page');
        }

        $count = $query->count();
        $pages = ceil($count / $limit);
        $page  = max(0, min($pages - 1, $page));

        $results = array_values($query->offset($page * $limit)->related('artist', 'user')->limit($limit)->orderBy($order[1], $order[2])->get());

        return compact('results', 'pages', 'count');
    }
}