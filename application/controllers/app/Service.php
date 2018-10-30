<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends App_Api_Controller {

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct('app_rest');
		$this->load->model('function/user_account_model','user_account');
		$this->load->model('function/service_function_model','service_function');
	}

	public function index(){
		$uid = $this->getUserId();
		if($uid){
			//呼叫服务
			if($this->service_function->call_service($uid)){
				$this->response($this->getResponseData(parent::HTTP_OK, '呼叫成功'), parent::HTTP_OK);
			}else{
				$this->response($this->getResponseData(parent::HTTP_OK, '呼叫失败，请重试'), parent::HTTP_OK);
			}
		}else{
			$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '登录信息失效，请重新登录'), parent::HTTP_OK);
		}
	}

}
