<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct(){
        header('Access-Control-Allow-Origin:*');
        header('Content-Type:application/json');
        parent::__construct();

        $this->load->model('adminuser_model','adminuser');

    }

    public function index()
    {
        
        $user_name = $this->input->get_post('username');
        $user_pass = $this->input->get_post('password');
        $return = [];
        if(isset($user_name) && isset($user_pass)){
            $res = $this->adminuser->get_info_by_username($user_name);
            if ($res->password === password_md5($user_pass)) {
                $_SESSION[ADMIN_SESSION_NAME]        =   $res->id;
                $_SESSION['username']   =   $res->username;
                $return['status'] = 'success';
                $return['detail'] = '登录成功';
            }else{
                $return['status'] = 'fail';
                $return['detail'] = '账号或密码错误';
                $return['password'] = $user_pass;
                $return['password_save'] = $res->password;
                $return['password_md5'] = password_md5($user_pass);
            }

        }else{
            $return['status'] = 'fail';
            $return['detail'] = '参数错误';
        }

        echo json_encode($return);
        exit();
        
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

    public function update()
    {
        $action = $this->input->get_post('action');

        if($action == 'password'){
            $admin_id = $this->session->userdata(ADMIN_SESSION_NAME);
            $password = password_md5($_POST['password']);
            if($this->adminuser->update($admin_id,array('password'=>$password))){
                echo 'success';
            }else{
                echo 'false';
            }
        }

    }



}
