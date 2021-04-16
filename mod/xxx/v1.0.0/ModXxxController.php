<?php

require_once DOC_ROOT . '/core2/inc/classes/class.list.php';
require_once DOC_ROOT . '/core2/inc/classes/class.edit.php';
require_once DOC_ROOT . '/core2/inc/classes/class.tab.php';
require_once DOC_ROOT . '/core2/inc/classes/Templater3.php';

class ModXxxController extends Common {

    public function __construct()
    {
        parent::__construct();

        $this->app = "?module=xxx";

    }

    public function action_index() {

        $tab = new \tabs('roles');
        $tab->beginContainer($this->translate->tr("Роли и доступ"));

            $list = new \listTable('roles');

            $list->table = "core_roles";

            $list->SQL = "
                    SELECT `id`,
                           `name`,
                           description,
                           position,
                           is_active_sw
                    FROM `core_roles` 
                    ORDER BY position
                ";

            $list->addColumn("asdasdas", "", "TEXT");
            $list->addColumn($this->translate->tr("Описание"), "", "TEXT");
            $list->addColumn($this->translate->tr("Иерархия"), "1%", "TEXT");
            $list->addColumn("", "1%", "STATUS");

            $list->paintCondition = "'TCOL_04' == 'N'";
            $list->paintColor     = "ffffee";

            $list->addURL    = $this->app . "&edit=0";
            $list->editURL   = $this->app . "&edit=TCOL_00";
            $list->deleteKey = "core_roles.id";

            $list->showTable();

        $tab->endContainer();
    }

    public function action_yyy() {
        $users = $this->db->fetchAll("SELECT * FROM core_users");
        $users = $this->modAdmin->dataModules->getModuleList();
        echo "<PRE>";print_r($users);
    }


}