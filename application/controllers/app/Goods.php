<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods extends App_Api_Controller {

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct('app_rest');
		$this->load->model('goods_model','goods');
		$this->load->model('log_expense_model','log_expense');
		$this->load->model('account_model','account');
		$this->load->model('good_type_model','good_type');
		$this->load->model('order_status_model','order_status');
		$this->load->model('function/user_account_model','user_account');
	}

	public function index(){
		$uid = $this->getUserId();

		$level = $this->user_account->get_member_level($uid);
		$discount = $this->config->item('discount_level')[$level];

		$list = $this->goods->get_list();
		$type_list = $this->good_type->get_list();

		foreach ($list as $key => $value) {
			$list[$key]['quantity'] = 0;
			$list[$key]['discount_price'] = round($value['price'] * $discount , 2);
		}

		$return_arr['good_list'] = $list;
		array_unshift($type_list,array('id'=>'0','name'=>'全部','status'=>'1'));
		$return_arr['type'] = $type_list;

		$return_arr['show'] = $level > 0 ? true : false;

		

		if($list && $type_list){
			$this->response($this->getResponseData(parent::HTTP_OK, '商品列表', $return_arr), parent::HTTP_OK);
		}else{
			$this->response($this->getResponseData(parent::HTTP_OK, '暂无商品', []), parent::HTTP_OK);
		}
		
	}

	public function buy(){
		$uid = $this->getUserId();
		if($uid){

			$total = $this->input->post_get('number');
			$list_json = $this->input->post_get('cartList');

			if(isset($total) && isset($list_json)){
				$list = json_decode($list_json);

				//执行事务 
				//分商品计入消费log
				//扣除账户余额
				$acc = $this->account->get_info($uid);
				if($acc->balance > $total){//先判断余额是否足够
					$this->db->trans_start();
					$total_money = 0;
					$log_ids = array();
					foreach ($list as $key => $value) {//计入消费记录
						$log_parm = [];
						$log_parm['uid'] = $uid;
						$log_parm['starttime'] = time();
						$log_parm['starttime'] = time();
						$log_parm['number'] = $value->quantity;
						$log_parm['price'] = $value->discount_price;
						$log_parm['money'] = round($value->quantity * $value->discount_price,2);
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
					$this->order_status->insert($order_status_parm);

					$this->account->expense($uid,$total);//账户扣款

					if($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '购买失败'), parent::HTTP_OK);
					}else{
						$this->db->trans_complete();
						//后续在此增加提醒订单的接口，用于与前台通信
						$this->response($this->getResponseData(parent::HTTP_OK, '购买成功'), parent::HTTP_OK);
					}
				}else{
					$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '余额不足'), parent::HTTP_OK);
				}
			}else{
				$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '订单信息有误'), parent::HTTP_OK);
			}
		}else{
			$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '登录信息过期，请重新登录'), parent::HTTP_OK);
		}
	}


}
