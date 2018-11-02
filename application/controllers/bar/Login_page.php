<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_page extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }


    public function index(){      
        $this->load->model('adminuser_model','adminuser');
        $action = $this->input->get('action');
        if($action == 'login'){
            $user_name = $this->input->post('username');
            $user_pass = $this->input->post('password');
            $res = $this->adminuser->get_info_by_username($user_name);
            if ($res->password === password_md5($user_pass)) {
                $_SESSION[ADMIN_SESSION_NAME]        =   $res->id;
                $_SESSION['username']   =   $res->username;
            }
            redirect(ADMIN_PATH);
        }  
        $this->load->view('bar/login');
    }


}
