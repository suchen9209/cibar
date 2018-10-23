<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends App_Api_Controller {

	public function index()
	{
		$this->load->view('bar/login');
	}
}
