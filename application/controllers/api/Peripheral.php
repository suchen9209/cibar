<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peripheral extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('peripheral_num_model','peripheral_num');

    }

    public function index(){
        $list = $this->peripheral_num->get_list_free();
        $type_name = $this->config->item('peripheral_type');
        $return_data['type_name'] = $type_name;
        $return_data['data'] = array();
        foreach ($list as $key => $value) {
           $return_data['data'][$value['type']] []= $value; 
        }
        $this->response($this->getResponseData(parent::HTTP_OK, '空闲外设', $return_data), parent::HTTP_OK);
    }





}
