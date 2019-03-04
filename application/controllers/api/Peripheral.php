<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peripheral extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('peripheral_num_model','peripheral_num');
        $this->load->model('peripheral_last_model','peripheral_last');
        $this->load->model('log_peripheral_in_model','log_peripheral_in');

    }

    public function index(){
        $uid = $this->input->get_post('user_id')?$this->input->get_post('user_id'):0;

        $list = $this->peripheral_num->get_list_free();

        $id_arr= [];
        if($uid != 0 ){
            $last = $this->peripheral_last->get_last_by_uid($uid);
            if($last){
                $tmp = json_decode($last->pid,true);
                $id_arr = array_column($tmp, 'id');
            }
        }
        
        $data = array();
        foreach ($list as $key => $value) {
            if(in_array($value['id'], $id_arr)){
                $value['last_use'] = true;
            }
            $data[$value['type']] []= $value; 
        }

        $type_name = $this->config->item('peripheral_type');
        $return_data['type_name'] = $type_name;

        $return_data['data'] = $data;
        
        
        $this->response($this->getResponseData(parent::HTTP_OK, '空闲外设', $return_data), parent::HTTP_OK);
    }

    public function last(){
        $uid = $this->input->get_post('user_id')?$this->input->get_post('user_id'):0;
        $last = $this->peripheral_last->get_last_by_uid($uid);
        $return_data = json_decode($last->pid);

        $this->response($this->getResponseData(parent::HTTP_OK, '上次使用外设', $return_data), parent::HTTP_OK);
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
                $this->peripheral_last->update($tmp->id,$parm);
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

    public function get_list(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;

        $offset = ($page-1)*$num;

        if($page && $num){
            $list = $this->peripheral_num->get_list($num,$offset);
            foreach ($list as $key => $value) {
                $list[$key]['user'] = $value['total'] - $value['count'];
                $list[$key]['type_name'] = $this->config->item('peripheral_type')[$value['type']];
            }
            $total = intval($this->peripheral_num->get_count());
            $count = $this->peripheral_num->get_type_num();
            foreach ($count as $key => $value) {
                $count[$key]['type_name'] = $this->config->item('peripheral_type')[$value['type']];
            }
            $return_arr = $this->getLayuiList(0,'外设列表',$total,$list);
            $return_arr['detail'] = $count;
            $this->response($return_arr);            
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }
    }

    public function get_need_in_list(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;

        $offset = ($page-1)*$num;
        $parm = array('state'=>0);
        $p_list = $this->log_peripheral_in->get_list($offset,$num,$parm);
        $p_num = $this->log_peripheral_in->get_num($parm);


        $this->response($this->getLayuiList(0,'在线用户列表',intval($p_num),$p_list));    
    }

    public function in(){
        $pnid = $this->input->get_post('pnid') ? $this->input->get_post('pnid') : 0;
        $ouid = 10;

        if($pnid > 0){
            $update_parm = array(
                'state'=>1,
                'intime'=>time(),
                'ouid'=>$ouid
            );
            if($this->log_peripheral_in->update($pnid,$update_parm)){
                $this->response($this->getResponseData(parent::HTTP_OK, '入库成功', 'success'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '入库错误', '请联系系统管理员'), parent::HTTP_OK);
            }    
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }

        

    }







}
