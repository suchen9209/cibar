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
        $this->load->model('account_model','account');
        $this->load->model('user_coupon_model','user_coupon');
        $this->load->model('coupon_model','coupon');
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
        $user_coupon_id = $this->input->get_post('user_coupon_id')?$this->input->get_post('user_coupon_id'):0;

/*        $list_json = '[{"id":4,"name":"红牛","price":5.00,"quantity":2,"type":2},{"id":3,"name":"巴黎水","price":8.00,"quantity":2,"type":2},{"id":2,"name":"依云","price":4.00,"quantity":2,"type":2},{"id":1,"name":"可乐","price":3.00,"quantity":2,"type":2}]';
        $user_coupon_id= 27;
        $uid = 57;*/
        $user_coupon_info = $this->user_coupon->get_info($user_coupon_id);
        $coupon_info = $this->coupon->get_info($user_coupon_info->cid);
        $discount_good_ids = $coupon_info->good_ids;
        $discount_good_ids = ','.$discount_good_ids.',';

        $list = json_decode($list_json);
        $id_name = array();

        $total_money = 0;
        $need_discount_goods = array();
        foreach ($list as $key => $value) {
            $tmp_number = $value->quantity;
            $tmp_price = $value->price;
            $total_money += $tmp_number * $tmp_price;
            if(strpos($discount_good_ids, ','.$value->id.',') !== false){
                for ($i=0; $i < $value->quantity; $i++) { 
                    $need_discount_goods []= array('id'=>$value->id,'price'=>$value->price,'name'=>$value->name);
                }
            }

            $id_name[$value->id] = $value->name;

        }
        array_multisort(array_column($need_discount_goods, 'price'),SORT_DESC,SORT_NUMERIC,$need_discount_goods);
        $reduce_money = 0;
        $reduce_good_list = array();
        for ($j=0; $j < $coupon_info->num; $j++) { 
            $reduce_money += (1-$coupon_info->discount)*$need_discount_goods[$j]['price'];
            if($reduce_good_list[$need_discount_goods[$j]['id']]){
                $reduce_good_list[$need_discount_goods[$j]['id']] ++;
            }else{
                $reduce_good_list[$need_discount_goods[$j]['id']] = 1;
            }
        }

        foreach ($reduce_good_list as $key => $value) {
            $return_data['reduce_good'] []= array('name' => $id_name[$key],'quantity'=>$value);
            # code...
        }

        $return_data['total_money'] = $total_money;//总价
        $return_data['discount_money'] = $total_money - $reduce_money;


        $this->response($this->getResponseData(parent::HTTP_OK, '结算信息', $return_data), parent::HTTP_OK);

    }



    public function buy(){

        $total = $this->input->post_get('total');
        $list_json = $this->input->post_get('cartList');
        $uid = $this->input->get_post('user_id') ? $this->input->get_post('user_id') : 0;
        $payment = $this->input->post_get('payment') ? $this->input->get_post('payment') : 0;
        $user_coupon_id = $this->input->post_get('user_coupon_id') ? $this->input->get_post('user_coupon_id') : 0;

        if(isset($total) && isset($list_json)){
            $list = json_decode($list_json);

            //执行事务 
            //分商品计入消费log
            //扣除账户余额
            $discount = 1;
            if($uid != 0 && $payment == 0){
                $acc = $this->account->get_info($uid);
                if($acc->balance < $total){//先判断余额是否足够
                    $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '余额不足'), parent::HTTP_OK);
                }
            }

            $this->db->trans_start();
            $total_money = 0;
            $log_ids = array();
            foreach ($list as $key => $value) {//计入消费记录
                $log_parm = [];
                $log_parm['uid'] = $uid;
                $log_parm['starttime'] = time();
                $log_parm['endtime'] = time();
                $log_parm['number'] = $value->quantity;
                $log_parm['price'] = $value->price;
                $log_parm['money'] = round($value->quantity * $value->price,2);
                $log_parm['type'] = $value->type;
                $log_parm['goodid'] = $value->id;

                $log_id = $this->log_expense->insert($log_parm);
                $log_ids []= $log_id;
                //$total_money += $value->number * $value->price;
            }

            $log_ids_str = implode(',', $log_ids);
            $order_status_parm['uid'] = $uid;
            $order_status_parm['createtime'] = time();
            $order_status_parm['log_ids'] = $log_ids_str;
            $order_status_parm['payment'] = $payment;
            $order_status_parm['status'] = $this->config->item('order_status_status')['done'];
            $order_status_parm['total'] = $total;
            $order_status_parm['user_coupon_id'] = $user_coupon_id;
            $order_status_id = $this->order_status->insert($order_status_parm);

            if($uid != 0 && $payment == 0){
                $this->account->expense($uid,$total);//账户扣款
            }

            if($user_coupon_id > 0){
                $this->user_coupon->use_coupon($user_coupon_id,0,$order_status_id);
            }

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '购买失败'), parent::HTTP_OK);
            }else{
                $this->db->trans_complete();
                $this->response($this->getResponseData(parent::HTTP_OK, '购买成功'), parent::HTTP_OK);
            }
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误'), parent::HTTP_OK);
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

            $uid = $value['uid'];
            $user_info = $this->user_account->get_user_info($uid);
            $list[$key]['username'] = $user_info['username'];
            $list[$key]['phone'] = $user_info['phone'];
            $list[$key]['balance'] = $user_info['balance'];
            $list[$key]['level'] = $user_info['level'];

            //特殊处理
            $list[$key]['aaa'] = '订单详情';
        }

        $num = $this->order_status->get_num($list_option);

        $this->response($this->getLayuiList(0,'未完成手机订单',intval($num),$list));   
    }

    public function done_order(){
        $order_id = $this->input->get_post('order_id') ? $this->input->get_post('order_id') : 0;
        if($order_id != 0){
            if($this->order_status->update($order_id,array('status' => $this->config->item('order_status_status')['done'] ) ) ){
                $this->response($this->getResponseData(parent::HTTP_OK, '更新成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更新失败'), parent::HTTP_OK);
            }
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '错误订单号'), parent::HTTP_OK);
        }
    }


}
