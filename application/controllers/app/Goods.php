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
	}

	public function index(){

		$list = $this->goods->get_list();
		$type_list = $this->good_type->get_list();

		foreach ($list as $key => $value) {
			$list[$key]['quantity'] = 0;
		}

		$return_arr['good_list'] = $list;
		array_unshift($type_list,array('id'=>'0','name'=>'全部','status'=>'1'));
		$return_arr['type'] = $type_list;

		if($list && $type_list){
			$this->response($this->getResponseData(parent::HTTP_OK, '商品列表', $return_arr), parent::HTTP_OK);
		}else{
			$this->response($this->getResponseData(parent::HTTP_OK, '暂无商品', []), parent::HTTP_OK);
		}
		
	}

	public function buy(){
		$uid = $this->getUserId();
		if($uid){
			$list_json = $this->input->post_get('list_json');
			$list = json_decode($list_json);

			//执行事务 
			//分商品计入消费log
			//扣除账户余额
			$acc = $this->account->get_info($uid);
			if($acc->balance > $list->total){//先判断余额是否足够
				$this->db->trans_start();
				$total_money = 0;
				foreach ($list->list as $key => $value) {//计入消费记录
					$log_parm = [];
					$log_parm['uid'] = $uid;
					$log_parm['starttime'] = time();
					$log_parm['starttime'] = time();
					$log_parm['number'] = $value->number;
					$log_parm['price'] = $value->price;
					$log_parm['money'] = $value->number * $value->price;
					$log_parm['type'] = $value->type;
					$log_parm['goodid'] = $value->id;

					$this->log_expense->insert($log_parm);
					$total_money += $value->number * $value->price;
				}

				if($total_money != $list->total){//核对金额
					$this->db->trans_rollback();
					$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '数额不匹配，刷新页面'), parent::HTTP_OK);
				}else{
					$this->account->expense($uid,$total_money);//账户扣款
					$result =  $this->db->trans_complete();

					//后续在此增加提醒订单的接口，用于与前台通信

					$this->response($this->getResponseData(parent::HTTP_OK, '购买成功'), parent::HTTP_OK);
				}
			}else{
				$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '余额不足'), parent::HTTP_OK);
			}
		}else{
			$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '登录信息过期，请重新登录'), parent::HTTP_OK);
		}
	}


}
