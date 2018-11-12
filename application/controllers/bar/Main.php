<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends Admin_Controller {

    public function __construct(){
        parent::__construct();
    }

	public function index()
	{

        $this->load->view('bar/admin/index');
        		
	}

	 /**
     * login函数
     * 后台管理员登录
     * @access public
     * @author Xu01
     *
     */
    public function login()
    {
        $action = $this->input->get('login');
        if($action == 'login'){
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

        $this->load->view('bar/login');
        
    }

    /**
     * logout函数
     * 后台管理员登出
     * @access public
     * @author Xu01
     *
     */
    public function logout()
    {
        session_unset();
        session_destroy();
        redirect(ADMIN_PATH);
    }
}
