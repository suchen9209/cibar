<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();
        //$session_name = 'user_id';
        $this->load->model('goods_model','goods');
        $this->load->model('log_expense_model','log_expense');
        $this->load->model('good_type_model','good_type');
        $this->load->model('order_status_model','order_status');
        $this->load->model('function/user_account_model','user_account');
    }

    public function index(){

        $list = $this->goods->get_list();
        $type_list = $this->good_type->get_list();
        $return_list = array();
        foreach ($list as $key => $value) {
            $return_list[$value['type']] []= $value;
        }
        $return_arr['good_list'] = $return_list;
        $return_arr['type'] = $type_list;
        

        if($list && $type_list){
            $this->response($this->getResponseData(parent::HTTP_OK, '商品列表', $return_arr), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_OK, '暂无商品', []), parent::HTTP_OK);
        }
        
    }

    public function calculate_discount(){
        $list_json = $this->input->post_get('cartList');
        $uid = $this->input->get_post('user_id')?$this->input->get_post('user_id'):0;

        $level = $this->user_account->get_member_level($uid);
        $discount = $this->config->item('discount_level')[$level];

        $list = json_decode($list_json);

        $total_money = 0;
        foreach ($list as $key => $value) {//计入消费记录
            $tmp_number = $value->quantity;
            $tmp_price = $value->price;
            $tmp_discount_price = round($value->price * $discount,2);

            $total_money += $tmp_number * $tmp_discount_price;
        }

        $this->response($this->getResponseData(parent::HTTP_OK, '折扣后金额', $total_money), parent::HTTP_OK);

    }



    public function buy(){

        $total = $this->input->post_get('number');
        $list_json = $this->input->post_get('cartList');
        $uid = $this->input->get_post('user_id')?$this->input->get_post('user_id'):0;

        if(isset($total) && isset($list_json)){
            $list = json_decode($list_json);

            //执行事务 
            //分商品计入消费log
            //扣除账户余额
            $discount = 1;
            if($uid != 0){
                $acc = $this->account->get_info($uid);
                if($acc->balance < $total){//先判断余额是否足够
                    $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '余额不足'), parent::HTTP_OK);
                }

                $level = $this->user_account->get_member_level($uid);
                $discount = $this->config->item('discount_level')[$level];
            }

            $this->db->trans_start();
            $total_money = 0;
            $log_ids = array();
            foreach ($list as $key => $value) {//计入消费记录
                $log_parm = [];
                $log_parm['uid'] = $uid;
                $log_parm['starttime'] = time();
                $log_parm['starttime'] = time();
                $log_parm['number'] = $value->quantity;
                $log_parm['price'] = $value->price;
                $log_parm['money'] = round($value->quantity * $value->price * $discount,2);
                $log_parm['type'] = $value->type;
                $log_parm['goodid'] = $value->id;

                $log_id = $this->log_expense->insert($log_parm);
                $log_ids []= $log_id;
                //$total_money += $value->number * $value->price;
            }

            if($uid != 0){
                $this->account->expense($uid,$total);//账户扣款
            }

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '购买失败'), parent::HTTP_OK);
            }else{
                $this->db->trans_complete();
                //后续在此增加提醒订单的接口，用于与前台通信
                $this->response($this->getResponseData(parent::HTTP_OK, '购买成功'), parent::HTTP_OK);
            }
        }
    }


    public function get_on_list(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $offset = ($page-1)*$num;

        $list_option = array(
            'order_status.status' => 0
        );
        $list = $this->order_status->get_list($offset,$num,$list_option);
        foreach ($list as $key => $value) {
            $log_ids = $value['log_ids'];
            $logids_arr = explode(',', $log_ids);

            $temp = [];
            foreach ($logids_arr as $kl => $vl) {
                $log_detail = $this->log_expense->get_detail($vl);
                $temp []= array(
                    'name'=>$log_detail->good_name,
                    'num'=>$log_detail->number,
                    'money'=>$log_detail->money
                );
            }
            $list[$key]['detail'] = $temp;
        }

        $num = $this->order_status->get_num($list_option);

        $this->response($this->getLayuiList(0,'未完成手机订单',intval($num),$list));   
    }

    public function done_order(){
        $order_id = $this->input->get_post('order_id') ? $this->input->get_post('order_id') : 0;
        if($order_id != 0){
            if($this->order_status->update($order_id,array('status'=>$this->config->item('order_status_status')['done']))){
                $this->response($this->getResponseData(parent::HTTP_OK, '更新成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更新失败'), parent::HTTP_OK);
            }
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '错误订单号'), parent::HTTP_OK);
        }
    }


}
