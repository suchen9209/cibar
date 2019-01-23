<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coupon extends App_Api_Controller {

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


        $this->response($this->getResponseData(parent::HTTP_OK,'优惠券列表',$data), parent::HTTP_OK);
    }

    public function coupon_list(){
        $uid = $this->getUserId();
        $type = $this->input->get_post('type') ? $this->input->get_post('type') : 0;
        $outdate = $this->input->get_post('outdate') ? $this->input->get_post('outdate') : 0;
        if($uid){
            if($outdate > 0){
                $data = $this->user_coupon->get_cannot_use_by_uid_type($uid,$type);
                $data2 = $this->user_coupon->get_used_coupon_by_uid_type($uid,$type);

                $return_data = array_merge($data,$data2);
                array_multisort(array_column($return_data, 'endtime'),SORT_DESC,SORT_NUMERIC,$return_data);
                $this->response($this->getResponseData(parent::HTTP_OK,'不可用优惠券列表',array_slice($return_data, 0,50)), parent::HTTP_OK);
            }else{
                $data = $this->user_coupon->get_can_use_by_uid_type($uid,$type);
                $this->response($this->getResponseData(parent::HTTP_OK,'可用优惠券列表',$data), parent::HTTP_OK);    
            }
            
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '登录信息过期，请重新登录'), parent::HTTP_OK);
        }
        
    }

}
