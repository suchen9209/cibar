<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peripheral extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('peripheral_num_model','peripheral_num');
        $this->load->model('peripheral_last_model','peripheral_last');

    }

    public function index(){
        $list = $this->peripheral_num->get_list_free();
        $type_name = $this->config->item('peripheral_type');
        $return_data['type_name'] = $type_name;
        $return_data['data'] = array();
        foreach ($list as $key => $value) {
           $return_data['data'][$value['type']] []= $value; 
        }
        $this->response($this->getResponseData(parent::HTTP_OK, '空闲外设', $return_data), parent::HTTP_OK);
    }

    public function out(){
        $uid = $this->input->get_post('user_id')?$this->input->get_post('user_id'):0;
        $json = $this->input->get_post('pjson');

        /*$json = '[{"type":1,"id":1},{"type":2,"id":3},{"type":3,"id":7},{"type":4,"id":5}]';*/

        if($uid != 0){
            $this->db->trans_start();
            $pdata = json_decode($json,true);
            foreach ($pdata as $key => $value) {
                $this->peripheral_num->out($value['id']);
            }

            $parm = array(
                    'uid'   =>  $uid,
                    'pid'   =>  $json
                );
            if($tmp = $this->peripheral_last->get_last_by_uid($uid)){
                $this->peripheral_last->update($tmp[0]['id'],$parm);
            }else{
                $this->peripheral_last->insert($parm);
            }

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '外设分配失败，刷新页面'), parent::HTTP_OK);
            }else{
                $this->db->trans_complete();
                $this->response($this->getResponseData(parent::HTTP_OK, '分配成功'), parent::HTTP_OK);
            }
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }

    }





}
