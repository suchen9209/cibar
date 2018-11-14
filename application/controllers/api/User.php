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
            $temp['level'] = $this->user_account->get_member_level($value['uid'],$value['total']);
            $temp['name'] = $value['name'];
            $temp['phone']  = $value['phone'];
            $temp['idcard']  = $value['idcard'];
            $temp['balance']  = $value['balance'];
            $temp['regtime']  = $value['regtime'];
            $temp['opentime'] = $value['updatetime'];
            $temp['machine_name'] = $value['machine_name'];
            $online_seconds = time() - $value['updatetime'];
            $temp['online_seconds'] = $online_seconds;

            $half_hour_num = ceil($online_seconds / 1800);//当前已经上网的半小时数
            $discount = $this->config->item('discount_level')[$temp['level']];//获取折扣
            $price = round($this->config->item('price')[$value['type']] * $discount,2);//计算单小时价格
            $temp['cost'] = round($half_hour_num * ($price/2) , 2);//计算当前已消费的金额

            
            $can_online_seconds = ceil($value['balance']/$price*3600);//计算当前余额可上网时长
            $temp['remain_seconds'] = $can_online_seconds - $online_seconds;//计算剩余时长

            $temp['box'] = $value['box_id'];
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

    public function get_user_num(){
        $num =  $this->user_account->get_user_num();
        $this->response($this->getResponseData(parent::HTTP_OK, '总人数', $num), parent::HTTP_OK);
    }

    public function get_user_list(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = 20;
        $order_option = $this->input->get_post('order_option') ? $this->input->get_post('order_option') : 'lasttime';
        $order = $this->input->get_post('order')  ? $this->input->get_post('order') : 'DESC';

        if($this->input->get_post('id'))$parm['id']=$this->input->get_post('id');
        if($this->input->get_post('phone'))$parm['phone']=$this->input->get_post('phone');
        if($this->input->get_post('idcard'))$parm['idcard']=$this->input->get_post('idcard');
        if($this->input->get_post('name'))$parm['name']=$this->input->get_post('name');
        if($this->input->get_post('username'))$parm['username']=$this->input->get_post('username');

        $offset = ($page-1)*$num;

        $user_num =  $this->user_account->get_user_num();

        if($page && in_array($order_option, ['balance','total','lasttime','regtime']) && in_array($order, ['ASC','DESC'])){
            $list = $this->user_account->get_user_list($num,$offset=0,$order_option,$order,$parm);
            $return_arr['list'] = $list;
            $return_arr['page_num'] = ceil($user_num/$num);
            $this->response($this->getResponseData(parent::HTTP_OK, '用户列表及总页数', $return_arr), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }

    }

    public function bind_info($type){

        $uid = $this->input->get_post('user_id');
        if($type == 'idcard'){
            $parm['idcard'] = $this->input->get_post('idcard');
            $parm['name'] = $this->input->get_post('name');
        }else if($type == 'phone'){
            $parm['phone'] = $this->input->get_post('phone');
        }

        if($this->user->update($uid,$parm)){
            $this->response($this->getResponseData(parent::HTTP_OK, '绑定成功'), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_OK, '绑定失败'), parent::HTTP_OK);
        }
        
    }

    //获取消费记录
    public function get_log_expense(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('num') ? $this->input->get_post('num') : 20;
        $uid = $this->input->get_post('uid') ? $this->input->get_post('uid') : 0;
        $offset = $num*($page-1);
        $this->load->model('log_expense_model','log_expense');
        if($uid>0){
            $log_num = $this->log_expense->get_num(array('uid'=>$uid));
            $return_data['list'] = $this->log_expense->get_list($offset,$num,array('uid'=>$uid));
        }else{
            $log_num = $this->log_expense->get_num();
            $return_data['list'] = $this->log_expense->get_list($offset,$num);

        }
        $return_data['page_num'] = ceil($log_num/$num);

        $this->response($this->getResponseData(parent::HTTP_OK, '消费记录', $return_data), parent::HTTP_OK);
    }

    //获取充值记录
    public function get_log_pay(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('num') ? $this->input->get_post('num') : 20;
        $uid = $this->input->get_post('uid') ? $this->input->get_post('uid') : 0;
        $offset = $num*($page-1);
        $this->load->model('log_pay_model','log_pay');
        if($uid>0){
            $log_num = $this->log_pay->get_num(array('uid'=>$uid));
            $return_data['list'] = $this->log_pay->get_list($offset,$num,array('uid'=>$uid));
        }else{
            $log_num = $this->log_pay->get_num();
            $return_data['list'] = $this->log_pay->get_list($offset,$num);

        }
        $return_data['page_num'] = ceil($log_num/$num);

        $this->response($this->getResponseData(parent::HTTP_OK, '充值记录', $return_data), parent::HTTP_OK);
    }


}
