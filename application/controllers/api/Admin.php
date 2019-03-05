<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function index(){

    	$ouid = $_SESSION['ouid'];
    	$this->load->model('adminuser_model','adminuser');
    	$admin_user_info = $this->adminuser->get_info($ouid);

        if($ouid > 0){
        	$data['name'] = $admin_user_info->name;
        	$data['phone'] = $admin_user_info->phone;
        	$data['role'] = $admin_user_info->role;
            $this->response($this->getResponseData(parent::HTTP_OK, '管理员信息', $data), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }
    }

}
