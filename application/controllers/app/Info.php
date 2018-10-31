<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Info extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
        header('Content-Type:application/json');

        $return_arr['ad'] = array(
            array(
                'id'=>1,
                'img'=>'https://bar.suchot.com/1.png'
            ),
            array(
                'id'=>2,
                'img'=>'https://bar.suchot.com/2.png'
            ),
            array(
                'id'=>3,
                'img'=>'https://bar.suchot.com/3.png'
            )
        );

        $return_arr['wifi'] = array(
            'account'   :   'IMBATV',
            'bssid'     :   '08:9b:4b:91:32:58',
            'password'  :   'imbaadmin'
        );

        echo json_encode($return_arr);
        
	}
}
