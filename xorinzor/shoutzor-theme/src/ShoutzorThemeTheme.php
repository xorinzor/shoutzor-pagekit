<?php

namespace Bixie\BixBlack;

use Pagekit\Application as App;
use Pagekit\Module\Module;

class ShoutzorThemeTheme extends Module {

    /**
     * @var App
     */
    protected $app;
    /**
     * @var Style\StyleManager
     */
    protected $styles;
    /**
     * {@inheritdoc}
     */
    public function main (App $app) {
        $this->app = $app;
        $this->styles = new Style\StyleManager($app);
    }

    public function isLoggedIn() {

    }
}
