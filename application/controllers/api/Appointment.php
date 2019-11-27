<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct('none_rest');
        $this->load->model('appointment_model','appointment');

    }

    public function index(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $offset = ($page - 1) * $num;
        $list = $this->appointment->get_appoint_after($offset,$num);
        $count = $this->appointment->get_appoint_after_num();
        $this->response($this->getLayuiList(0,'预约列表',$count,$list));
    }

    public function num(){
        $list = $this->appointment->get_appoint_today();
        $num = array(
        	'散座'	=> 0,
        	'5人包厢' 	=> 0,
        	'6人包厢' 	=> 0,
        	'10人包厢' 	=> 0,
        	'20人包厢' 	=> 0,
        );
        foreach ($list as $key => $value) {
        	if( $value['type'] == $this->config->item('seat_type')['seat'] ){
        		$num['散座'] += $value['number'];
        	}

        	if( $value['type'] == $this->config->item('seat_type')['box'] ){
        		$num[$value['number'].'人包厢'] ++;
        	}
        }
        $this->response($this->getResponseData(parent::HTTP_OK, '今日预约数量', $num), parent::HTTP_OK);

    }




}
