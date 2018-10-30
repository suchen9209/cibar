<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends Test_Api_Controller {

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct('app_rest');
		$this->load->model('function/user_account_model','user_account');
		$this->load->model('function/user_seat_model','user_seat');
	}

	public function index(){
		$uid = $this->getUserId();
		if($uid){
			//呼叫服务
			$seat_info = $this->user_seat->get_seat($uid);
			$this->load->driver('cache');
			$service_json = $this->cache->memcached->get('service');
			$service = json_decode($service_json,true);
			$service[]= $seat_info;
			$this->cache->memcached->save('service',json_encode($service),3600*24);

			$this->response($this->getResponseData(parent::HTTP_OK, '呼叫成功'), parent::HTTP_OK);
		}else{
			$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '登录信息失效，请重新登录'), parent::HTTP_OK);
		}
		
	}

}
