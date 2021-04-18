<?php

require_once DOC_ROOT . '/core2/inc/classes/class.list.php';
require_once DOC_ROOT . '/core2/inc/classes/class.edit.php';
require_once DOC_ROOT . '/core2/inc/classes/class.tab.php';
require_once DOC_ROOT . '/core2/inc/classes/Templater3.php';
require_once DOC_ROOT . '/mod/articles/v1.0.0/Article.php';

class ModArticlesController extends Common {

    public function __construct()
    {
        parent::__construct();

        $this->app = "?module=articles";

    }

    public function action_index() {

        $tab = new \tabs('articles');
        $article = new Article();
        $title = $this->translate->tr("Статьи на сайте");
        if (isset($_GET['edit']) && $_GET['edit'] === '0') {
            $article->create();
            $title = $this->translate->tr("Создание новой статьи");

        } elseif ( ! empty($_GET['edit'])) {
            $article->get($_GET['edit']);
            $title = sprintf($this->translate->tr('Редактирование статьи "%s"'), $article->id);
        }
        $tab->beginContainer($title);

        echo $article->dispatch();

        $tab->endContainer();

    }

}


