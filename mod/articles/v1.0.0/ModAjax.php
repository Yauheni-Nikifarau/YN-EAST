<?php


use Laminas\Session\Container as SessionContainer;


require_once("core2/inc/ajax.func.php");


/**
 * Class ModAjax
 * @property UsersProfile $dataUsersProfile
 * @property Users $dataUsers
 */
class ModAjax extends ajaxFunc
{


    /**
     * @param xajaxResponse $res
     */
    public function __construct(xajaxResponse $res)
    {
        parent::__construct($res);
        $this->module = 'articles';
    }

    public function saveArticle ($data) { //TODO: доделать

        $refid  = $this->getSessFormField($data['class_id'], 'refid');
        $fields = [
            'title'     => 'req',
            'text'  => 'req',
            'author' => 'req'
        ];


        $data['control']['title']  = trim(strip_tags($data['control']['title']));
        $data['control']['text']   = trim(strip_tags($data['control']['text']));
        $data['control']['author'] = trim(strip_tags($data['control']['author']));


        $dataForSave = [
            'title'         => $data['control']['title'],
            'text'           => $data['control']['text'],
            'author'        => $data['control']['author']
        ];


        if ($this->ajaxValidate($data, $fields)) {
            return $this->response;
        }


        $this->db->beginTransaction();

        try {



            if ($refid == 0) {
                $this->db->insert('articles', $dataForSave);
                $refid = $this->db->lastInsertId('articles');
            } else {
                $where  = $this->db->quoteInto('id = ?', $refid);
                $this->db->update('articles', $dataForSave, $where);
            }

            if ($refid) {
                $save = [
                    'title'   => $data['control']['title'],
                    'text'  => $data['control']['text'],
                    'author' => $data['control']['author']
                ];

               $row  = $this->dataArticles->fetchRow(
                    $this->dataArticles->select()->where("title = ?", $save['title'])->limit(1)
                );
                /*
                if ( ! $row) {
                    $row = $this->dataArticles->createRow();
                    $save['id'] = $refid;
                } else {
                    $data['control']['id'] = $this->dataArticles->fetchRow(
                        $this->dataArticles->select()->where("id = ?", $refid)->limit(1)
                    )->id;
                }
                */
                $row->setFromArray($save);
                $row->save();
            }


            $this->db->commit();

        } catch (Exception $e) {
            $this->db->rollback();
            $this->error[] = $e->getMessage();
        }

        $this->done($data);
        return $this->response;
    }

}