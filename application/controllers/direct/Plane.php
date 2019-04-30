<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plane extends Admin_Api_Controller {

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

    public function num(){
        $this->load->model('appointment_model','appointment');
        $list = $this->appointment->get_appoint_today();
        $num = array(
            '散座'    => 0,
            '5人包厢'  => 0,
            '6人包厢'  => 0,
            '10人包厢'     => 0,
            '20人包厢'     => 0,
        );
        foreach ($list as $key => $value) {
            if( $value['type'] == $this->config->item('seat_type')['seat'] ){
                $num['散座'] += $value['number'];
            }

            if( $value['type'] == $this->config->item('seat_type')['box'] ){
                $num[$value['number'].'人包厢'] ++;
            }
        }
        $this->response($this->getResponseData(parent::HTTP_OK, '今日预约数量', $num), parent::HTTP_OK);

    }



}
