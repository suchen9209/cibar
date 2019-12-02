<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends App_Api_Controller {

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct('none_rest');
		$this->load->model('news_model','news');
	}

	public function index(){
		$type = $this->input->get_post('type')?$this->input->get_post('type'):1;

		$list = $this->news->get_list(0,6,array('type'=>$type));
		$this->response($this->getResponseData(parent::HTTP_OK, '预约页', $list), parent::HTTP_OK);
	}

	public function activity(){
		$return['top'] = $this->news->get_list(0,6,array('type'=>2));
		$return['bottom'] = $this->news->get_list(0,10,array('type'=>3));
		$this->response($this->getResponseData(parent::HTTP_OK, '活动页', $return), parent::HTTP_OK);
	}

	public function detail($id){
		$new = $this->news->get_info($id);
		if($new){
			$this->response($this->getResponseData(parent::HTTP_OK, '详情', $new), parent::HTTP_OK);
		}else{
			$this->response($this->getResponseData(parent::HTTP_NOT_FOUND, '详情', '未查到相关活动'), parent::HTTP_OK);
		}
		
	}

}
