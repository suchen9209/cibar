<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ad extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
        header('Content-Type:application/json');
        $return_arr = array('https://bar.suchot.com/1.png','https://bar.suchot.com/2.png','https://bar.suchot.com/3.png');

        echo json_encode($return_arr);
        
	}
}
