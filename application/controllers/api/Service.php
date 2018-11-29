<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('function/service_function_model','service_function');

    }

    public function index(){
        $list = $this->service_function->get_all_service();
        if($list){
            $this->response($this->getResponseData(parent::HTTP_OK, '当前呼叫列表', $list), parent::HTTP_OK);    
        }else{
            $this->response($this->getResponseData(parent::HTTP_OK, '当前呼叫列表', []), parent::HTTP_OK);
        }
        

    }
}