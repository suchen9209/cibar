<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends App_Api_Controller {

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct('app_rest');
		$this->load->model('function/user_account_model','user_account');
	}

	public function index(){
		$uid = $this->getUserId();
		$level = $this->user_account->get_member_level($uid);
		$member_id = member_id($uid);

		if($uid){
			$user_info = $this->user_account->get_user_info($uid);
			$user_info['level'] = $level;
			$user_info['memberid'] = $member_id;

			$this->response($this->getResponseData(parent::HTTP_OK, '用户信息', $user_info), parent::HTTP_OK);
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
