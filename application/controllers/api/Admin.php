<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('adminuser_model','adminuser');
    }

    public function index(){
    	$ouid = $_SESSION['ouid'];
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

    public function change_password(){
    	$ouid = $_SESSION['ouid'];
    	$admin_user_info = $this->adminuser->get_info($ouid);

    	$old_password = $this->input->get_post('old_password');
        $new_password = $this->input->get_post('new_password');
        if(isset($old_password) && isset($new_password) && $ouid){
            if ($admin_user_info->password === password_md5($old_password)) {
            	if($this->adminuser->update($ouid,array('password'=>password_md5($new_password)))){
            		$this->response($this->getResponseData(parent::HTTP_OK, '修改成功', 'success'), parent::HTTP_OK);
            	}else{
            		$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '修改失败', 'success'), parent::HTTP_OK);
            	}                
            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '原密码错误', 'nothing'), parent::HTTP_OK);
            }
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }
    }

}
