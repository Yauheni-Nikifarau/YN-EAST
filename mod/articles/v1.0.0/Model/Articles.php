<?php
class Articles extends Zend_Db_Table_Abstract
{
    protected $_name = 'articles';


    /**
     * @param string $expr
     * @param array  $var
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function exists($expr, $var = array()) {
        $sel = $this->select()->where($expr, $var);

        return $this->fetchRow($sel->limit(1));
    }

    /**
     * Получаем значение одного поля
     *
     * @param $field
     * @param $expr
     * @param array $var
     * @return string
     */
    public function fetchOne($field, $expr, $var = array())
    {
        $sel = $this->select();
        if ($var) {
            $sel->where($expr, $var);
        } else {
            $sel->where($expr);
        }
        return $this->fetchRow($sel)->$field;
    }


    /**
     * @param string $id
     * @return mixed
     */
    public function getArticleById($id) {
        $res   = $this->_db->fetchRow("
            SELECT *
            FROM `articles` AS a
            WHERE a.id = ? 
            LIMIT 1
        ", $id);

        return $res;
    }

}