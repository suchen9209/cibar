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
                'img'=>'https://pay.imbatv.cn/1.png'
            ),
            array(
                'id'=>2,
                'img'=>'https://pay.imbatv.cn/2.png'
            ),
            array(
                'id'=>3,
                'img'=>'https://pay.imbatv.cn/3.png'
            ),
            array(
                'id'=>4,
                'img'=>'https://pay.imbatv.cn/4.png'
            ),
        );

        $return_arr['wifi'] = array(
            'account'   =>   'IMBATV',
            'bssid'     =>   '08:9b:4b:91:32:58',
            'password'  =>   'imbaadmin'
        );

        echo json_encode($return_arr);
        
	}
}
