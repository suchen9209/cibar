<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coupon extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('coupon_model','coupon');
        $this->load->model('user_coupon_model','user_coupon');

    }

    public function index(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $offset = ($page - 1) * $num;

        $data = $this->coupon->get_list($offset,$num);
        $count = $this->coupon->get_num();


        $this->response($this->getLayuiList(0,'优惠券列表',$count,$data));
    }

    public function user_coupon_list_by_uid(){
        $uid = $this->input->get_post('user_id') ? $this->input->get_post('user_id') : 0;
        $type = $this->input->get_post('type') ? $this->input->get_post('type') : 0;

        if($uid > 0){
            $data = $this->user_coupon->get_can_use_by_uid_type($uid,$type);

            $this->response($this->getLayuiList(0,'优惠券列表',$count,$data));
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误'), parent::HTTP_OK);
        }
        
    }

}
