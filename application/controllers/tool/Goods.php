<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('goods_model','goods');
        $this->load->model('good_type_model','good_type');

    }

    public function index(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $offset = ($page - 1)*$num;

        //商品种类
        $type = $this->good_type->get_list(-1);
        $type_list = array_column($type, 'name' ,'id');

        

        $list = $this->goods->get_list(1,$num,$offset);
        foreach ($list as $key => $value) {
            $list[$key]['type'] = $type_list[$value['type']];
            $list[$key]['status'] = $this->config->item('status_common')[$value['status']];
        }
        $count = $this->goods->get_num()->num;
        $return_arr = $this->getLayuiList(0,'商品列表',$count,$list);
        $return_arr['type_list'] = $type_list;
        $return_arr['status_list'] = $this->config->item('status_common');
        $this->response($return_arr);     
    }

    public function good_info($id){
        $type = $this->good_type->get_list(-1);
        $type_list = array_column($type, 'name' ,'id');


        $data['type_list'] = $type_list;
        $data['status_list'] = $this->config->item('status_common');
        
        $data['data'] = $this->goods->get_info($id);
        $this->response($this->getResponseData(parent::HTTP_OK, '商品信息',$data), parent::HTTP_OK);
    }

    public function config_info(){
        //商品种类
        $type = $this->good_type->get_list(-1);
        foreach ($type as $key => $value) {
            $type_list []= array('id'=>$value['id'],'name'=>$value['name']);
        }

        $return_arr['type_list'] = $type_list;
        $return_arr['status_list'] = $this->config->item('status_common');

        $this->response($this->getResponseData(parent::HTTP_OK, '商品类型和状态',$return_arr), parent::HTTP_OK);
    }

	public function insert(){
        if($_POST['name'] && $_POST['img'] && $_POST['type'] && $_POST['price'] && $_POST['status']){
            $parm = $_POST;
            unset($parm['submit']);
            if($this->goods->insert($parm)){
                $this->response($this->getResponseData(parent::HTTP_OK, '增加成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '增加失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
	}

    public function update($id){
        if($_POST['name'] && $_POST['img'] && $_POST['type'] && $_POST['price'] && $_POST['status']){
            $parm = $_POST;
            unset($parm['submit']);
            if($this->goods->update($id,$parm)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }


}
