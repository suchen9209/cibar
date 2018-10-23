<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_login extends ImbaTV_Controller {
    public function __construct(){
        parent::__construct();
    	require_once(dirname(__FILE__)."/API/qqConnectAPI.php");
        
    }

    public function index(){
        $this->load->view('common/qq');        
    }

    public function login(){
        $qc = new QC();
        $qc->qq_login();
    }


    public function logout(){
        $redirect_url = $_GET['reurl']?$_GET['reurl']:'//www.imbatv.cn';
        session_unset();
        session_destroy();
        
        redirect($redirect_url);
    }





}