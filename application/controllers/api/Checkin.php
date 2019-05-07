<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Checkin extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('machine_model','machine');
        $this->load->model('machine_info_model','machine_info');
        $this->load->model('active_status_model','active_status');
        $this->load->model('box_status_model','box_status');
        $this->load->model('log_login_model','log_login');
        $this->load->model('peripheral_num_model','peripheral_num');
        $this->load->model('peripheral_last_model','peripheral_last');
        $this->load->model('function/send_wokerman_model','send_wokerman');

    }

    public function single_info(){
        $uid = $this->input->get_post('user_id') ? $this->input->get_post('user_id') : 0;

        $data['machine_type'] =$this->config->item('machine_type');
        $data['machine_price'] =$this->config->item('price');
        $data['machines'] = $this->machine->get_active_machine();  
        $mid_by_box = array();
        foreach ($data['machines'] as $key => $value) {
            $mid_by_box[$value['box_id']] []= $value;
        }
        $data['machines'] = $mid_by_box;
        $data['box_list'] = $this->machine->get_all_box(array('active_status.state'=>1));
        $tmp = array();
        foreach ($data['box_list'] as $key => $value) {
            $tmp[$value['type']] []= $value['box_id'];
        }
        $tmp["1"] = ['散座'];
        $data['box_list'] = $tmp;
        $data['box_type'] =$this->config->item('machine_type');
        unset($data['box_type'][1]);
        $data['box_price'] =$this->config->item('box_price');
        $data['pay_type'] = $this->config->item('box_status_pay_type');
        if($uid != 0){
            $list = $this->peripheral_num->get_list_free();

            $id_arr= [];
            $last = $this->peripheral_last->get_last_by_uid($uid);
            if($last){
                $tmp = json_decode($last->pid,true);
                $data['last_use'] = $tmp;
                $id_arr = array_column($tmp, 'id');
                $is_null = false;
            }else{
                $is_null = true;
            }
            
            $plist = array();
            foreach ($list as $key => $value) {
                if(in_array($value['id'], $id_arr)){
                    $value['last_use'] = true;
                }
                if($is_null && !isset($plist[$value['type']])){
                    $value['last_use'] = true;
                }
                $plist[$value['type']] []= $value; 
            }

            $type_name = $this->config->item('peripheral_type');
            $data['peripheral_type_name'] = $type_name;
            $data['peripheral_list'] = $plist;
        }


        $this->response($this->getResponseData(parent::HTTP_OK, '上机初始信息', $data), parent::HTTP_OK);

    }

    public function single(){
        $uid = $this->input->get_post('user_id');
        $san_or_box = $this->input->get_post('san_or_box');
        $pjson = $this->input->get_post('pjson');

        if(isset($uid) && isset($san_or_box) && $uid>0){
            if(!$this->active_status->get_info_uid($uid)){

                $this->db->trans_start();

                if($san_or_box == 'san'){
                    $machine_id = $this->input->post_get('machine_id');
                }else if($san_or_box == 'box'){
                    $box_id = $this->input->post_get('box_id');
                    $pay_type = $this->input->post_get('pay_type') ?  $this->input->post_get('pay_type') : 2;
                    $whopay = $this->input->post_get('whopay') ? $this->input->post_get('whopay') : $uid;

                    //随机分配一台该包厢的空机器
                    //后讨论仍改为前台手动选择，对应公安系统
/*                    $machine_list = $this->machine->get_active_machine_in_box($box_id);
                    $machine_id = $machine_list[0]['id'];*/
                    $machine_id = $this->input->post_get('machine_id');

                    $box_price = $this->config->item('box_price')[$machine_list[0]['type']];


                    $box_status_parm['uid'] = $uid;
                    $box_status_parm['box_id'] = $box_id;
                    $box_status_parm['pay_type'] = $pay_type;
                    $box_status_parm['box_price'] = $box_price;
                    $box_status_parm['whopay'] = $whopay;
                    $this->box_status->insert($box_status_parm);

                }

                //登录记录
                $log_parm['uid'] = $uid;
                $log_parm['login_type'] = $this->config->item('log_login_type')['bar'];
                $log_parm['machine_id'] = $machine_id;
                $log_parm['time'] = time();
                $this->log_login->insert($log_parm);

                //更新机器状态
                $active_parm['uid'] = $uid;
                $active_parm['state'] = 2;
                $active_parm['updatetime'] = time();                
                $this->active_status->update($machine_id,$active_parm);

                //外设出库
                $pdata = json_decode($pjson,true);
                foreach ($pdata as $key => $value) {
                    $this->peripheral_num->out($value['id']);
                }
                $parm = array(
                    'uid'   =>  $uid,
                    'pid'   =>  $pjson
                );
                //记录最近一次的外设信息
                $tmp = $this->peripheral_last->get_last_by_uid($uid);
                if($tmp){
                    $this->peripheral_last->update($tmp->id,$parm);
                }else{
                    $this->peripheral_last->insert($parm);
                }


                if($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '失败'), parent::HTTP_OK);
                }else{
                    //发送开机指令，整包使用时，发送包厢开机

                    $this->db->trans_complete();                    

                    $send_parm = array();
                    $send_parm['uid'] = $uid;
                    $send_parm['mid'] = $machine_id;
                    $send_parm['cmd'] = 'open';
                    $this->send_wokerman->send(json_encode($send_parm));
                    $this->response($this->getResponseData(parent::HTTP_OK, '登记成功'), parent::HTTP_OK);
                }  

            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '该用户为上机状态，不可重复上机'), parent::HTTP_OK);
            }            
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }
    }



}
