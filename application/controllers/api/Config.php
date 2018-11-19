<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends Admin_Api_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
        $type_list = $this->config->item('log_pay_type_cn');
        $this->response($this->getResponseData(parent::HTTP_OK, '支付类型', $type_list), parent::HTTP_OK);
	}
}
