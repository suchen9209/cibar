<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct('none_rest');

        $this->load->model('machine_model','machine');
        $this->load->model('machine_info_model','machine_info');
        $this->load->model('active_status_model','active_status');
        $this->load->model('box_status_model','box_status');
        $this->load->model('log_login_model','log_login');
        $this->load->model('log_expense_model','log_expense');
        $this->load->model('log_peripheral_in_model','log_peripheral_in');
        $this->load->model('log_play_model','log_play');
        $this->load->model('log_deduct_money_model','log_deduct_money');
        $this->load->model('peripheral_num_model','peripheral_num');
        $this->load->model('peripheral_last_model','peripheral_last');
        $this->load->model('function/send_wokerman_model','send_wokerman');
        $this->load->model('function/user_account_model','user_account');
        $this->load->model('user_model','user');
        $this->load->model('user_coupon_model','user_coupon');
        $this->load->model('account_model','account');
        $this->load->model('coupon_model','coupon');

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

        $data['machines'] =$this->machine->get_all_machine();

        $this->response($this->getResponseData(parent::HTTP_OK, '所有机器', $data), parent::HTTP_OK);
    }

    public function status(){
        $page = $this->input->get_post('page')?$this->input->get_post('page'):1;
        $num = $this->input->get_post('num')?$this->input->get_post('num'):30;

        $dirt = array_flip($this->config->item('active_status'));

        $offset = ($page - 1)*$num;
        $data = $this->active_status->get_active_machine_limit($offset,$num);
        $return_data = array();
        foreach ($data as $key => $value) {
            if($value['state'] == $dirt['正在使用']){
                $return_data []=$value;
            }
        }
        $this->response($this->getResponseData(parent::HTTP_OK, '在线机器详细信息', $return_data), parent::HTTP_OK);
    }

    //获取在线用户的信息
    public function get_active_user_info(){
        $uid = $this->input->get_post('user_id') ? $this->input->get_post('user_id') : 0;
        if($uid > 0){
            $user_info = $this->user_account->get_user_info($uid);
            $login_info = $this->log_login->get_last_login_info($uid);
            $user_info['opentime'] = $login_info->time;
            $user_info['duration'] = time() - $login_info->time;
            $this->response($this->getResponseData(parent::HTTP_OK, '用户信息', $user_info), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }
    }

    public function check_status_by_ip(){
        $ip = $this->input->get_post('ip') ? $this->input->get_post('ip') : '';
        if($ip != ''){
            $machine = $this->machine->get_machine_by_ip($ip);
            $active_status_info = $this->active_status->get_info_mid($machine->id);
            if($active_status_info->state == 2){
                $this->response($this->getResponseData(parent::HTTP_OK, '允许开机', '已在前台登录'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '禁止开机', '未在前台登录'), parent::HTTP_OK);
            }

        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'ip无法获取'), parent::HTTP_OK);
        }
    }



}
