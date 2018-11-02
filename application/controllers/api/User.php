<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('function/user_account_model','user_account');
        $this->load->model('user_model','user');
        $this->load->model('active_status_model','active_status');

    }

    public function index(){
        $uid = $this->input->get_post('user_id');
        $phone = $this->input->get_post('phone');

        if($uid){
            $user_info = $this->user_account->get_user_info($uid);
            $this->response($this->getResponseData(parent::HTTP_OK, '用户信息', $user_info), parent::HTTP_OK);
        }else if($phone){
            $user = $this->user->get_info_u('phone',$phone);
            $user_info = $this->user_account->get_user_info($user->id);
            $this->response($this->getResponseData(parent::HTTP_OK, '用户信息', $user_info), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }   
    }

    public function pay(){
        $uid = $this->input->get_post('user_id');
        $num = $this->input->post_get('number');
        $type = $this->input->post_get('type');
        $extra_number = $this->input->post_get('extra_number');

        if(isset($uid) && isset($num) && isset($type) && isset($extra_number) && $uid>0){
            $log_parm['uid'] = $uid;
            $log_parm['time'] = time();
            $log_parm['money'] = $num;
            $log_parm['pay_type'] = $type;
            //暂时写死
            //$log_parm['operator'] = $this->session->admin_id;
            $log_parm['operator'] = 100;
            $log_parm['extra_num'] = $extra_number;

            if($this->user_account->add_balance($uid,$log_parm)){
                $this->response($this->getResponseData(parent::HTTP_OK, '充值成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '充值失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }

    }

    public function get_live_user(){
        $page = $this->input->get_post('page');
        $num = $this->input->get_post('num');
        if(!isset($page)){$page=1;}
        if(!isset($num)){$num=20;}
        $offset = ($page-1)*$num;

        $users = $this->active_status->get_live_user($offset,$num);
        $return_arr = [];
        foreach ($users as $key => $value) {
            $temp['uid'] = $value['uid'];
            $temp['username'] = $value['username'];
            $temp['level'] = $this->user_account->get_member_level($value['uid']);
            $temp['name'] = $value['name'];
            $temp['phone']  = $value['phone'];
            $temp['idcard']  = $value['idcard'];
            $temp['balance']  = $value['balance'];
            $return_arr[]=$temp;
        }
        $this->response($this->getResponseData(parent::HTTP_OK, '用户信息', $return_arr), parent::HTTP_OK);
    }

    public function get_detail_info(){
        $uid = $this->input->get_post('user_id');
        $phone = $this->input->get_post('phone');

        if($uid){
            $user_info = $this->user_account->get_user_info($uid);
            $this->response($this->getResponseData(parent::HTTP_OK, '用户信息', $user_info), parent::HTTP_OK);
        }else if($phone){
            $user = $this->user->get_info_u('phone',$phone);
            $user_info = $this->user_account->get_user_info($user->id);
            $this->response($this->getResponseData(parent::HTTP_OK, '用户信息', $user_info), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }   
    }


}
