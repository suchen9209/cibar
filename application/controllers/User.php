<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('user_model','user');
	}

	public function index()
	{
		var_dump($_SESSION);
		var_dump(checkUserLogin());
		$user_info = $this->user->get_user_info(1);
		var_dump($user_info);die;

		$this->load->view('welcome_message');
	}

	public function forcelogin(){
		
		$this->session->user_id = 1;
		var_dump($this->session);
    }

    public function forcelogout(){
    	session_destroy();
    }
}
