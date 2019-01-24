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
		$this->load->model('active_status_model','active_status');
		$this->load->model('function/user_account_model','user_account');
		$this->load->model('function/send_wokerman_model','send_wokerman');
		$this->load->model('coupon_model','coupon');
        $this->load->model('user_coupon_model','user_coupon');
	}

	public function index(){
		$uid = $this->getUserId();

		$level = $this->user_account->get_member_level($uid);
		$discount = $this->config->item('discount_level')[$level];

		$list = $this->goods->get_list();
		$type_list = $this->good_type->get_list();

		foreach ($list as $key => $value) {
			$list[$key]['quantity'] = 0;
			//$list[$key]['discount_price'] = round($value['price'] * $discount , 2);//19.01.15应店长要求去除小商品的折扣
			$list[$key]['discount_price'] = $value['price'];
		}

		$return_arr['good_list'] = $list;
		array_unshift($type_list,array('id'=>'0','name'=>'全部','status'=>'1'));
		$return_arr['type'] = $type_list;

		$return_arr['show'] = false;

		//获取能使用优惠券的商品id
		$user_coupons = $this->user_coupon->get_can_use_by_uid_type($uid,2);
		$good_ids_need_handle = array();
		foreach ($user_coupons as $key => $value) {
			$good_ids_need_handle = array_merge($good_ids_need_handle,explode(',', $value['good_ids']));
		}
		$good_ids_need_handle = array_unique($good_ids_need_handle);
		array_multisort($good_ids_need_handle);

		$return_arr['coupon_good_ids'] = implode(',', $good_ids_need_handle);


		

		if($list && $type_list){
			$this->response($this->getResponseData(parent::HTTP_OK, '商品列表', $return_arr), parent::HTTP_OK);
		}else{
			$this->response($this->getResponseData(parent::HTTP_OK, '暂无商品', []), parent::HTTP_OK);
		}
		
	}

	public function calculate_discount(){
        $list_json = $this->input->post_get('cartList');
        $uid = $this->getUserId();
        $user_coupon_id = $this->input->get_post('user_coupon_id')?$this->input->get_post('user_coupon_id'):0;

/*        $list_json = '[{"id":4,"name":"红牛","price":5.00,"quantity":2,"type":2},{"id":3,"name":"巴黎水","price":8.00,"quantity":2,"type":2},{"id":2,"name":"依云","price":4.00,"quantity":2,"type":2},{"id":1,"name":"可乐","price":3.00,"quantity":2,"type":2}]';
        $list_json = '[{"id":4,"name":"红牛","price":5.00,"quantity":1,"type":2},{"id":3,"name":"巴黎水","price":8.00,"quantity":1,"type":2},{"id":2,"name":"依云","price":4.00,"quantity":1,"type":2}]';
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
        for ($j=0; $j < $coupon_info->num && $j < count($need_discount_goods); $j++) { 
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
		$uid = $this->getUserId();
		if($uid){
			$total = $this->input->post_get('number');
			$list_json = $this->input->post_get('cartList');
			$user_coupon_id = $this->input->get_post('user_coupon_id')?$this->input->get_post('user_coupon_id'):0;

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
						$log_parm['endtime'] = time();
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
					$order_status_parm['user_coupon_id'] = $user_coupon_id;
					$order_status_id = $this->order_status->insert($order_status_parm);

					$this->account->expense($uid,$total);//账户扣款

					if($user_coupon_id > 0){
		                $this->user_coupon->use_coupon($user_coupon_id,0,$order_status_id);
		            }

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
			if($list){
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
				$this->response($this->getResponseData(parent::HTTP_NOT_FOUND, '暂无订单'), parent::HTTP_OK);
			}
			
			
		}else{
			$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '登录信息过期，请重新登录'), parent::HTTP_OK);
		}
	}


}
