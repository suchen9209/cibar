<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends Admin_Api_Controller {

	public function __construct(){
		parent::__construct();
        $this->load->model('coupon_model','coupon');
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

        //当前可以用优惠券
        $return_data['coupon_list'] = $this->coupon->get_list(0,100,array('state'=>1));
        $this->response($this->getResponseData(parent::HTTP_OK, '支付类型', $return_data), parent::HTTP_OK);
	}

}
