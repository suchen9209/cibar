<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends App_Api_Controller {

	public function __construct(){

		$session_name = $this->input->post_get('3rd_session');
		//$session_name = 'user_id';
		
		parent::__construct('app_rest',$session_name);
		$this->load->model('function/user_account_model','user_account');
	}

	public function index(){
		//session判断
		//$uid = $_SESSION['user_id'];
		$uid = $_SESSION[$this->get_session_name()];

		//$uid = $this->input->get('uid');
		//get_uid
		$user_info = $this->user_account->get_user_info($uid);

		$this->response($this->getResponseData(parent::HTTP_OK, '用户信息', $user_info), parent::HTTP_OK);
	}

	public function pay(){
		//uid,number

		$uid = $this->input->get('uid');
		$number = $this->input->get('post');

	}

}
