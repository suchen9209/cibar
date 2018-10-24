<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends App_Api_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('function/user_account_model','user_account');
	}

	public function index(){
		//session判断
		//$uid = $_SESSION['user_id'];
		$uid = $this->input->get('uid');
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
