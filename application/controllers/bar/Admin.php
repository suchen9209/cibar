<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('adminuser_model','adminuser');

    }

    public function index(){
        $page = $this->input->get('page');
        $page = $page ? $page : 1;
        $num = 20;
        $data['admin_data'] = $this->adminuser->get_admin_list($page,$num);
        
        $this->load->view('bar/admin/list',$data);
    }

	public function register()
	{
        $action = $this->input->get('action');
        if($action == 'register'){
            $parm = $_POST;
            unset($parm['submit']);
            $parm['password'] = password_md5($parm['password']);
            $this->adminuser->insert($parm);
            redirect(ADMIN_PATH.'/admin');
            exit();
        }
        
		$this->load->view('bar/register');
	}

    public function login()
    {
        $user_name = $this->input->post('username');

        $user_pass = $this->input->post('password');
        $res = $this->sys_users->get_info_by_username($user_name);
        $salt = '0xdeadbeef';
        if ($res && is_array($res)) {
            if (strtolower($res[0]['user_pass']) === md5(md5($user_pass . $salt))) {
                $_SESSION['username']   =   $res[0]['user_name'];
                $_SESSION['realname']   =   $res[0]['realname'];
                $_SESSION['uid']        =   $res[0]['uid'];
                $_SESSION['role']       =   $res[0]['role'];
            }
        }
        redirect(ADMIN_PATH);
    }


}
