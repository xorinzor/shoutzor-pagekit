<ul class="uk-nav uk-nav-side">
    <?php
        foreach ($root->getChildren() as $node):
            if($node->hasChildren() === false) {
                echo '<li class="' . ($node->get('active') ? 'uk-active' : '') . '"><a href="' . $node->getUrl() . '">' . $node->title . '</a></li>';
            }
        endforeach;
    ?>
</ul>