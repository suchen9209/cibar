<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends Test_Api_Controller {

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct('app_rest');
		$this->load->model('function/user_account_model','user_account');
	}

	public function index(){
		$uid = $this->getUserId();
		if($uid){
			$user_info = $this->user_account->get_user_info($uid);

			$this->response($this->getResponseData(parent::HTTP_OK, '用户信息', $user_info), parent::HTTP_OK);
		}else{
			$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '用户信息', 'nothing'), parent::HTTP_OK);
		}
		
	}

}
