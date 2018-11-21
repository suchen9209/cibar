<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('machine_model','machine');
        $this->load->model('machine_info_model','machine_info');
        $this->load->model('active_status_model','active_status');
        $this->load->model('log_login_model','log_login');
        $this->load->model('peripheral_num_model','peripheral_num');
        $this->load->model('peripheral_last_model','peripheral_last');

    }

    public function index(){

        $type = $this->input->get_post('type');
        $data['machine_type'] =$this->config->item('machine_type');
        if($type){
            $data['machines'] =$this->machine->get_active_machine($type);
        }else{
            
            $data['machines'] = $this->machine->get_active_machine();  
        }
        $this->response($this->getResponseData(parent::HTTP_OK, '机器剩余情况', $data), parent::HTTP_OK);

    }

    public function all(){
        $type = $this->input->get_post('type');
        $data['machine_type'] =$this->config->item('machine_type');

        $data['machines'] =$this->machine->get_all_machine(array('machine.status'=>1));

        $this->response($this->getResponseData(parent::HTTP_OK, '所有机器', $data), parent::HTTP_OK);
    }

    public function order(){
        $uid = $this->input->get_post('user_id');
        $machine_id = $this->input->post_get('machine_id');

        if(isset($uid) && isset($machine_id) && $uid>0){

            if(!$this->active_status->get_info_uid($uid)){
                $log_parm['uid'] = $uid;
                $log_parm['login_type'] = $this->config->item('log_login_type')['bar'];
                $log_parm['machine_id'] = $machine_id;
                $log_parm['time'] = time();
                $log_parm['login_or_logout'] = $this->config->item('log_login')['login'];

                $this->db->trans_start();
                $this->log_login->insert($log_parm);
                $active_parm['uid'] = $uid;
                $active_parm['state'] = 2;
                $active_parm['updatetime'] = time();
                $this->active_status->update($machine_id,$active_parm);


                if($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $this->response($this->getResponseData(parent::HTTP_OK, '失败'), parent::HTTP_OK);
                }else{
                    $this->db->trans_complete();
                    //发送开机指令
                    $this->response($this->getResponseData(parent::HTTP_OK, '登记成功'), parent::HTTP_OK);
                }  
            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '该用户为上机状态，不可重复上机'), parent::HTTP_OK);
            }
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }
    }

    public function down(){
        $uid = $this->input->get_post('user_id');
        $op = $this->input->get_post('op');

        if(isset($uid) && $uid > 0){
            $ac_temp = $this->active_status->get_info_uid($uid);
            $machine_id = $ac_temp->mid;
            $machine_info = $this->machine->get_info($machine_id);
            if($op == 'get'){
                $this->response($this->getResponseData(parent::HTTP_OK, 'sucess',$machine_info), parent::HTTP_OK);
            }else if($op == 'down'){
                $log_parm['uid'] = $uid;
                $log_parm['login_type'] = $this->config->item('log_login_type')['bar'];
                $log_parm['machine_id'] = $machine_id;
                $log_parm['time'] = time();
                $log_parm['login_or_logout'] = $this->config->item('log_login')['logout'];

                $this->db->trans_start();
                $this->log_login->insert($log_parm);
                $active_parm['uid'] = 0;
                $active_parm['state'] = 1;
                $active_parm['updatetime'] = time();
                $this->active_status->update($machine_id,$active_parm);

                if($last_p = $this->peripheral_last->get_last_by_uid($uid)){
                    $pjson = $last_p[0]['pid'];
                    $pdata = json_decode($pjson,true);
                    foreach ($pdata as $key => $value) {
                        $this->peripheral_num->in($value['id']);
                    }
                }

                if($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $this->response($this->getResponseData(parent::HTTP_OK, '失败'), parent::HTTP_OK);
                }else{
                    $this->db->trans_complete();
                    //发送关机指令
                    $this->response($this->getResponseData(parent::HTTP_OK, '已下机'), parent::HTTP_OK);
                }

            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
            }
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }
        
    }


}
