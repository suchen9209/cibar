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

    public function give(){
        $uid = $this->input->get_post('user_id');
        $num = $this->input->get_post('number')?$this->input->get_post('number'):1;
        $cid = $this->input->get_post('coupon_id')?$this->input->get_post('coupon_id'):0;

        if(isset($uid) && isset($num) && isset($cid) && $uid>0 && $cid>0){

            $this->db->trans_start();

            $coupon_info = $this->coupon->get_info($cid);

            for ($i=0; $i < $num; $i++) { 
                $user_coupon_parm = array();
                $user_coupon_parm['uid'] = $uid;
                $user_coupon_parm['cid'] = $cid;
                $user_coupon_parm['starttime'] = time();
                $user_coupon_parm['endtime'] = time() + $coupon_info->validity * 24 * 60 * 60;
                $user_coupon_parm['state'] = 1;
                $user_coupon_parm['log_pay_id'] = 0;
                $this->user_coupon->insert($user_coupon_parm);    
            }          

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '赠送失败'), parent::HTTP_OK);
            }else{
                $this->db->trans_complete();
                $this->response($this->getResponseData(parent::HTTP_OK, '赠送成功'), parent::HTTP_OK);
            } 
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }
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
