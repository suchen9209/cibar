<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct('none_rest');

        $this->load->model('function/user_account_model','user_account');
        $this->load->model('account_model','account');
        $this->load->model('user_model','user');
        $this->load->model('active_status_model','active_status');
        $this->load->model('log_login_model','log_login');
        $this->load->model('log_pay_model','log_pay');
        $this->load->model('box_status_model','box_status');
        $this->load->model('log_deduct_money_model','log_deduct_money');
        $this->load->model('coupon_model','coupon');
        $this->load->model('user_coupon_model','user_coupon');      

    }


    //获取在线用户的信息
    public function get_active_user_info(){
        $uid = $this->input->get_post('user_id') ? $this->input->get_post('user_id') : 0;
        if($uid > 0){
            $user_info = $this->user_account->get_user_info($uid);
            $login_info = $this->log_login->get_last_login_info($uid);
            $user_info['opentime'] = $login_info->time;
            $user_info['duration'] = time() - $login_info->time;

            $deduct_info = $this->log_deduct_money->get_total_info($uid);
            if($deduct_info){
                $user_info['cost'] = $deduct_info['total_money'];
            }else{
                $user_info['cost'] = 0;
            }

            $this->response($this->getResponseData(parent::HTTP_OK, '用户信息', $user_info), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }
    }

}
