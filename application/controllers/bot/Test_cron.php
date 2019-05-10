<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_cron extends Ci_Controller {

	private $uid;
	private $utime;//本次扣款对应的上机时长
	private $mid;//机器ID
	private $box_id;//包厢ID
	private $type;//散客 or 整包
	private $price;//单价
	private $this_price;//本次扣款价格
	private $toplimit;//通宵上限价格
	private $pay_uid;//付款人ID
	private $true_price_before_discount;//本次扣款计算完通宵后，打折前的价格
	private $memcached_id;

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct();
		$this->load->model('function/user_account_model','user_account');
		$this->load->model('function/send_wokerman_model','send_wokerman');
		$this->load->model('account_model','account');
		$this->load->model('appointment_model','appointment');
		$this->load->model('machine_model','machine');
        $this->load->model('machine_info_model','machine_info');
        $this->load->model('active_status_model','active_status');
        $this->load->model('box_status_model','box_status');
        $this->load->model('log_login_model','log_login');
        $this->load->model('log_deduct_money_model','log_deduct_money');
	}

	public function get_mac($id)
	{
		$id = intval($id);
		$id++;
		if($id > 240){
			echo 'over';
			return 'over';
		}

		echo '进行到id:'.$id.'<br/>';
		$machine_detail = $this->machine->get_info($id);

		if(!$machine_detail->ip){
			$update_parm = array();
			$update_parm['mac'] = '暂无';
			$update_parm['machine_info'] = '未同步';
			$update_parm['repair_info'] = '未安装系统';
			if($this->machine_info->update($machine_detail->id,$update_parm)){
				echo $id.'更新machine_info成功<br/>';
			}else{
				echo $id.'更新machine_info失败<br/>';
			}

			if($this->active_status->update($machine_detail->id,array('state'=>4))){
				echo $id.'更新active_status成功<br/>';
			}else{
				echo $id.'更新active_status失败<br/>';
			}

			if($this->machine->update($machine_detail->id,array('status'=>2))){
				echo $id.'更新machine成功<br/>';
			}else{
				echo $id.'更新machine失败<br/>';
			}
		}else{
			$url = "http://status.golgaming.com:8443/api/v1/launch?jtname=macaddr&jthost=".$machine_detail->ip;
			// 1. 初始化
			 $ch = curl_init();
			 // 2. 设置选项，包括URL
			 curl_setopt($ch,CURLOPT_URL,$url);
			 curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			 curl_setopt($ch,CURLOPT_HEADER,0);
			 // 3. 执行并获取HTML文档内容
			 $output = curl_exec($ch);
			 if($output === FALSE ){
			 echo "CURL Error:".curl_error($ch);
			 }
			 $get_detail = json_decode($output);
			if($get_detail->code == 200){
				var_dump($get_detail->data->vals[0]);
			}
			curl_close($ch);
			die;
			$update_parm = array();

			echo '<br/>'.$id.'存在ip，预留';
		}

		sleep(1);
		
		
		die;

		 exit('<script language="javascript" type="text/javascript">
window.location.href="http://pay.imbatv.cn/bot/test_cron/get_mac/'.$id.'";
</script>');
		# code...
	}

	public function index(){

		$this->utime = $this->input->get_post('utime')?$this->input->get_post('utime'):180;
		
		$test = $this->active_status->get_all_live_user_id();
		foreach ($test as $key => $value) {

			$this->uid = $value['uid'];
			
			//判断用户是否是上机状态
			$active_status_info = $this->active_status->get_info_uid($this->uid);
			if($active_status_info){
				$this->mid = $active_status_info->mid;
				$machine_info = $this->machine->get_info($this->mid);
				//判断是整包上网还是散客上网
				$box_status = $this->box_status->get_info_uid($this->uid);
				if($box_status){//整包
					$this->type = 'box';
					$this->box_id = $box_status->box_id;
					$this->memcached_id = 'box_'.$this->box_id.'_total';

					//获取当前包厢的使用人数
					$num = $this->box_status->get_num_by_box_id($this->box_id);
					if($num == 0){
						echo 'error';
						exit();
					}

					//获取单价
					$box_price = $this->config->item('box_price')[$machine_info->type];//获取包厢总价
					$this->price = round($box_price / $num ,2);
					//获取包厢的通宵价格上限
					$this->toplimit = $this->config->item('box_overtime_price')[$machine_info->type];
					$this->pay_uid = $box_status->whopay;//付款人	


				}else{//散客
					$this->type = 'san';
					$this->memcached_id = 'san_'.$this->uid.'_total';
					
					//获取单价
					$this->price = $this->config->item('price')[$machine_info->type];


					if($machine_info->type == 1){
						$this->toplimit = 75;
					}else{
						$this->toplimit = 100;
					}

					$this->pay_uid = $this->uid;//付款人	
				}
				//本次扣款原价
				$this->this_price = round($this->price*($this->utime / 3600),2);

				$this->db->trans_start();
				$this->overnignt_logic();//判断通宵的逻辑
				$log_parm = array();
				$log_parm['uid'] = $this->uid;
				$log_parm['pay_uid'] = $this->pay_uid;
				$log_parm['time'] = date('YmdHis');
				$log_parm['price'] = $this->price;
				$log_parm['money'] = $this->true_price_before_discount;
				//根据付款人的id获取折扣
				$discount = $this->config->item('discount_level')[$this->user_account->get_member_level($this->pay_uid)];
				$discount_price = round($this->true_price_before_discount * $discount , 2);
				$log_parm['discount_money'] = $discount_price; 
				$log_parm['type'] = $this->type == 'san' ? 1 : 2;

				$this->log_deduct_money->insert($log_parm);

				$deduct_info = $this->log_deduct_money->get_total_info($this->uid);

				$remain_money = $this->account->get_info($this->pay_uid)->balance - $deduct_info['total_money'];
				if($remain_money < $this->config->item('critical_value')){
					$send_parm = array();
			        $send_parm['uid'] = $this->uid;
			        $send_parm['mid'] = $this->mid;
			        $send_parm['cmd'] = 'not_enough_money';
			        $this->send_wokerman->send(json_encode($send_parm));
				}

				//扣款放在下机时进行，此处只记录呼吸扣款额
				//$this->account->expense($this->pay_uid,$discount_price);

				if($this->db->trans_status() === FALSE){
	                $this->db->trans_rollback();
	                echo 'error2';
	            }else{
	                $this->db->trans_complete();
	                echo 'success';
	            }
			}else{
				//error log
				echo 'error';
			}

		}
	}

	//判断是否还需要扣钱,返回应扣金额
	private function overnignt_logic(){//type san->散客，box->整包
		$this->load->driver('cache');
		$today_0_time = strtotime('00:00:00');//今日0点的时间戳
		if((time() - $today_0_time) < 7*3600){//在0-7点之间
			$num = $this->cache->memcached->get($this->memcached_id);
			if( !$num ){
				$num = 0;
			}
			if($num + $this->this_price > $this->toplimit){
				if($num >= $this->toplimit){
					$this->true_price_before_discount = 0;
				}else{
					$this->cache->memcached->save($this->memcached_id,$num + $this->this_price,3600*7);
					$this->true_price_before_discount = $num + $this->this_price - $this->toplimit;
				}
			}else{
				$this->cache->memcached->save($this->memcached_id,$num + $this->this_price,3600*7);
				$this->true_price_before_discount = $this->this_price;
			}
		}else{
			$this->true_price_before_discount = $this->this_price;
		}

	}


}
