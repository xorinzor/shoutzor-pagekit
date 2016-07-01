<div class="uk-panel uk-panel-box">
    <div class="uk-panel-title">
        <p>Search Results for '<?= $searchterm; ?>' - Found <?= $resultCount; ?> Result(s)</p>
    </div>

    <?= $view->render('shoutzor:views/elements/tracks-table.php', ['tracks' => $results]); ?>

    <?= $view->render('shoutzor:views/elements/search-pagination.php', ['route' => '@shoutzor/search', 'page' => $page, 'total' => $totalPage, "q" => $searchterm]); ?>
</div>
