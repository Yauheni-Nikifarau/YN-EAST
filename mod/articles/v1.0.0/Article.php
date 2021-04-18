<?php
class Article extends Common
{
    private $app   = "index.php?module=articles";
    private $_article;
    private $_info = ['cols' => []]; //перечень всех полей


    /**
     * User constructor.
     */
    public function __construct() {

        parent::__construct();
        $this->_info = $this->dataArticles->info();
    }


    /**
     * @param string $name
     * @return \Common|\CoreController|mixed|\Zend_Config_Ini|\Zend_Db_Adapter_Abstract|null
     * @throws \Exception
     */
    public function __get($name) {

        if ( ! in_array($name, $this->_info['cols']))  {
            return parent::__get($name);
        } else {
            return $this->_article->$name;
        }
    }


    /**
     * получаем данные по id
     * @param $id - id юзера
     * @throws \Exception
     */
    public function get($id) {
        $this->_article = $this->dataArticles->find($id)->current();
        if ( ! $this->_article) {
            throw new \Exception(404);
        }
    }

    /**
     * форма редактирования
     * @return false|string
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Exception
     */
    public function edit() {

        $edit = new \editTable('articles');

        $fields = [
            'id',
            'title',
            'text',
            'author'
        ];

        $edit->SQL = $this->db->quoteInto("
                SELECT " . implode(",\n", $fields) . "
                FROM articles
                WHERE `id` = ?
            ", $this->_article->id);


        $edit->addControl("Заголовок:", "TEXT", "maxlength=\"300\" style=\"width:385px\"", "", "", true);
        $edit->addControl("Текст:",              "TEXT", "maxlength=\"60\" style=\"width:385px\"", "", "");
        $edit->addControl("Автор:",     "TEXT", "style=\"width:385px\"", "", "", true);


        $edit->addButton($this->_("Вернуться к списку статей"), "load('$this->app')");
        $edit->save("xajax_saveArticle(xajax.getFormValues(this.id))");

        ob_start();
        $edit->showTable();
        return ob_get_clean();
    }


    /**
     * Создание новой статьи
     */
    public function create() {
        $this->_article = $this->dataArticles->createRow();
    }


    /**
     * вывод
     * @return false|string
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Exception
     */
    public function dispatch() {

        if ($this->_article) {
            return $this->edit();
        } else {
            $list = new \listTable('articles');

            $list->table = "articles";

            $list->SQL = "
                    SELECT *
                    FROM articles 
                    ORDER BY id
                ";

            $list->addColumn($this->translate->tr("Заголовок"), "5%", "TEXT");
            $list->addColumn($this->translate->tr("Текст"), "", "TEXT");
            $list->addColumn($this->translate->tr("Автор"), "5%", "TEXT");
            $list->addColumn("create", "3%", "DATE");
            $list->addColumn("edit", "3%", "DATE");

            $list->paintCondition = "'TCOL_04' == 'Y'";
            $list->paintColor     = "ff0000";

            $list->addURL    = $this->app . "&edit=0";
            $list->editURL   = $this->app . "&edit=TCOL_00";
            $list->deleteKey = "articles.id";

            $list->showTable();
        }
    }



}