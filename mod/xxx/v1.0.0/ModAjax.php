<?php

use Laminas\Session\Container as SessionContainer;


require_once("core2/inc/ajax.func.php");


/**
 * Class ModAjax
 * @property UsersProfile $dataUsersProfile
 * @property Users        $dataUsers
 */
class ModAjax extends ajaxFunc {


    /**
     * @param xajaxResponse $res
     */
    public function __construct (xajaxResponse $res) {
		parent::__construct($res);
		$this->module = 'admin';
	}

    /**
     * Сохранение роли пользователя
     * @param array $data
     * @return xajaxResponse
     */
    public function saveRole($data) {

        $fields = array('name' => 'req', 'position' => 'req');
        if ($this->ajaxValidate($data, $fields)) {
            return $this->response;
        }
        $refid = $this->getSessFormField($data['class_id'], 'refid');
        if ($refid == 0) {
            $data['control']['date_added'] = new \Zend_Db_Expr('NOW()');
        }
        if (!isset($data['access'])) $data['access'] = array();
        $data['control']['access'] = serialize($data['access']);
        if (!$last_insert_id = $this->saveData($data)) {
            return $this->response;
        }
        if ($refid) {
            $this->cache->clearByTags(array('role' . $refid));
        }

        $this->done($data);
        return $this->response;
    }
}