<?php
/**
 * Views Plugin
 *
 * @package Views
 * @author  Double
 * @version 1.0.1
 * @link    http://blog.hequanxi.com
 */

class Views_Action extends Typecho_Widget implements Widget_Interface_Do
{
    private $db;

    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
        $this->db = Typecho_Db::get();
    }

    /**
     * 点赞Likes
     */
    public function up(){
        $cid=$this->request->filter('int')->cid;
        if($cid){
            try {
                $row = $this->db->fetchRow($this->db->select('likesNum')->from('table.contents')->where('cid = ?', $cid));
                $this->db->query($this->db->update('table.contents')->rows(array('likesNum' => (int)$row['likesNum']+1))->where('cid = ?', $cid));
                $this->response->throwJson("success");
            } catch (Exception $ex) {
               echo $ex->getCode();
            }
        }  else {
            echo "error";
        }

    }

    public function action(){
        $this->on($this->request->is('up'))->up();
        $this->response->goBack();
    }
}