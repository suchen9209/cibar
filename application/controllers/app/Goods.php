<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods extends Test_Api_Controller {

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct('app_rest');
		$this->load->model('goods_model','goods');
		$this->load->model('log_expense_model','log_expense');
		$this->load->model('account_model','account');
		$this->load->model('good_type_model','good_type');
		$this->load->model('order_status_model','order_status');
		$this->load->model('active_status_model','active_status');
		$this->load->model('function/user_account_model','user_account');
		$this->load->model('function/send_wokerman_model','send_wokerman');
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
					$order_status_parm['total'] = $total;
					$order_status_id = $this->order_status->insert($order_status_parm);

					$this->account->expense($uid,$total);//账户扣款

					if($this->db->trans_status() === FALSE){
						$this->db->trans_rollback();
						$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '购买失败'), parent::HTTP_OK);
					}else{
						$this->db->trans_complete();

						$send_parm = array();

						$user_info = $this->active_status->get_info_uid($uid);
				        $send_parm['uid'] = $uid;
				        $send_parm['mid'] = $user_info->mid;
				        $send_parm['order_id'] = $order_status_id;
				        $send_parm['cmd'] = 'new_order';
				        $this->send_wokerman->send(json_encode($send_parm));
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

	public function order_list(){
		$uid = $this->getUserId();
		$page = $this->input->post_get('page')?$this->input->post_get('page'):1;
		$limit = $this->input->post_get('limit')?$this->input->post_get('limit'):5;
		$offset = ($page-1)*$limit;
		if($uid){
			$list = $this->order_status->get_list($offset,$limit,array('user.id'=>$uid));
			foreach ($list as $key => $value) {
				$log_ids = explode(',', $value['log_ids']);
				$list[$key]['total_money'] = 0;
				foreach ($log_ids as $k2 => $v2){
					$tmp_detail = $this->log_expense->get_detail($v2);
					$tmp_good = array();
					$tmp_good['name'] = $tmp_detail->good_name;
					$tmp_good['img'] = $tmp_detail->good_img;
					$tmp_good['price'] = $tmp_detail->good_price;
					$tmp_good['discount_price'] = $tmp_detail->price;
					$tmp_good['num'] = $tmp_detail->number;
					$list[$key]['detail'][] = $tmp_good;
					$list[$key]['total_money'] += $tmp_detail->money;
				}
			}
			$this->response($this->getResponseData(parent::HTTP_OK, '订单列表', $list), parent::HTTP_OK);
		}else{
			$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '登录信息过期，请重新登录'), parent::HTTP_OK);
		}
	}


}
