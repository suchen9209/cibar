<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pay extends App_Api_Controller {

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct('app_rest');
		$this->load->model('function/user_account_model','user_account');
		$this->load->model('log_pay_model','log_pay');
		$this->load->model('account_model','account');
	}

	public function index(){
		//session判断
		//$uid = $_SESSION['user_id'];
		$uid = $this->getUserId();
		if($uid){
			//增加充值记录
			$num = $this->input->post_get('number');
			if(is_numeric($num)){

				$log_parm['uid'] = $uid;
				$log_parm['time'] = time();
				$log_parm['money'] = $num;
				$log_parm['pay_type'] = $this->config->item('log_pay_type')['wx'];
				$log_parm['operator'] = $uid;
				if($this->user_account->add_balance($uid,$log_parm)){
					$return_arr['time']=$log_parm['time'];
					$return_arr['money'] = $log_parm['money'];
					$this->response($this->getResponseData(parent::HTTP_OK, '充值成功', $return_arr), parent::HTTP_OK);
				}else{
					$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, 'error', '参数错误，充值金额必须为数字'), parent::HTTP_OK);
				}


			}else{
				$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, 'error', '参数错误，充值金额必须为数字'), parent::HTTP_OK);
			}
			//增加余额和总额
			//判断会员等级？（暂不需要
			
			
		}else{
			$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, 'error', '请联系管理员'), parent::HTTP_OK);
		}

	}

	public function add(){
		$uid = $this->getUserId();
		$num = $this->input->post_get('number');
	}


}
