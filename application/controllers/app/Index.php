<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends App_Api_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('function/user_account_model','user_account');
	}

	public function index()
	{

		$uid = $_SESSION['user_id'];
		$user_info = $this->user_account->get_user_info($uid);

		$this->response($this->getResponseData(parent::HTTP_OK, '用户信息', $user_info), parent::HTTP_OK);
	}

}
