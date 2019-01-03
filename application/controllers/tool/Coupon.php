<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coupon extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('coupon_model','coupon');
        $this->load->model('user_coupon_model','user_coupon');
        $this->load->model('goods_model','goods');

    }

    public function index(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $offset = ($page - 1) * $num;

        $data = $this->coupon->get_list($offset,$num);
        $count = $this->coupon->get_num();

        foreach ($data as $key => $value) {
            $good_id_arr = [];
            $good_name_str = '';
            if($value['good_ids'] != 0){
                $good_id_arr = explode(',', $value['good_ids']);
                $good_names = [];
                foreach ($good_id_arr as $kk => $vv) {
                    $good_names []= $this->goods->get_info($vv)->name;
                }
                $good_name_str = implode(',', $good_names);
            }
            $data[$key]['good_names'] = $good_name_str;
        }

        $this->response($this->getLayuiList(0,'优惠券列表',$count,$data));
    }

	public function insert()
	{
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"name":"饮品",status":"1"}';*/
        $data = json_decode($data_json,true);
        
        if($data){
            if($this->coupon->insert($data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '增加成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '增加失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
	}

    public function config_info(){
        
        $list = $this->goods->get_list(1,-1,-1,array('type'=>2));
        $return_arr['drink_list'] = $list;

        foreach ($this->config->item('coupon_type') as $key => $value) {
            $temp []= array('id'=>$key,'name'=>$value);
        }
        $return_arr['coupon_type'] = $temp;

        foreach ($this->config->item('coupon_state') as $key => $value) {
            $temp2 []= array('id'=>$key,'name'=>$value);
        }
        $return_arr['coupon_state'] = $temp2;

        $this->response($this->getResponseData(parent::HTTP_OK, '饮品列表及优惠券字段属性',$return_arr), parent::HTTP_OK);
    }

    public function update($id=0){
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"name":"饮品",status":"1"}';*/
        $data = json_decode($data_json,true);
        
        if($data && $id>0){
            if($this->coupon->update($id,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更新成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更新失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }

    }

    public function delete($id){
        if($this->coupon->delete($id)){
            $this->response($this->getResponseData(parent::HTTP_OK, '删除成功'), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_OK, '删除失败'), parent::HTTP_OK);  
        }

    }

}
