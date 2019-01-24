<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Center extends App_Api_Controller {

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct('app_rest');
		$this->load->model('function/user_account_model','user_account');
		$this->load->model('appointment_model','appointment');
        $this->load->model('user_coupon_model','user_coupon');
	}

	public function index(){
		//session判断
		//$uid = $_SESSION['user_id'];
		$uid = $this->getUserId();

		if($uid){
			$user_info = $this->user_account->get_user_info($uid);
			$appoint = $this->appointment->get_appoint_indate($uid);
			$have_coupon = $this->user_coupon->have_coupon($uid);


			$return['user_info'] = $user_info;
			$return['appoint_info'] = $appoint;
			$return['have_coupon'] = $have_coupon;

			$this->response($this->getResponseData(parent::HTTP_OK, 'user&appoint', $return), parent::HTTP_OK);
		}else{
			$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '用户信息', 'nothing'), parent::HTTP_OK);
		}
		
	}

	public function check_user_state(){
		$this->response($this->getResponseData(parent::HTTP_OK, '是否允许', 1), parent::HTTP_OK);
	}

	public function pay(){
		//uid,number

		$uid = $this->input->get('uid');
		$number = $this->input->get('post');

	}

}
