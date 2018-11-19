<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends Admin_Api_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
        $return_data = array();
        $type_list = $this->config->item('log_pay_type_cn');
        $tmp_type = array();
        foreach ($type_list as $key => $value) {
            $tmp['id'] = $key;
            $tmp['value'] = $value;
            $tmp_type []= $tmp;
        }
        $return_data['pay_type'] = $tmp_type;
        $this->response($this->getResponseData(parent::HTTP_OK, '支付类型', $return_data), parent::HTTP_OK);
	}
}
