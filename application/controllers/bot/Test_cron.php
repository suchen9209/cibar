<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_cron extends Ci_Controller {

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct();
        $this->load->model('active_status_model','active_status');
	}

	public function index(){

		$uids = $this->active_status->get_all_live_user_id();
		foreach ($uids as $key => $value) {
			$temp_uid = $value['uid'];
			$url = "https://pay.imbatv.cn/bot/deduct/index?uid=".$temp_uid."&utime=60";
			$ch = curl_init();  
		    curl_setopt($ch, CURLOPT_URL, $url);  
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回    
		    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回    
		    $r = curl_exec($ch);  
		    curl_close($ch);  
		}
	}


}
