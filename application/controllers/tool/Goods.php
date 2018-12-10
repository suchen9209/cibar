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
        $this->response($this->getLayuiList(0,'商品列表',$count,$list));     
    }

	public function insert(){
        $action = $this->input->get('action');
        if($_POST){
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
        $parm = $_POST;
        unset($parm['submit']);
        if($this->goods->update($id,$parm)){
            $this->response($this->getResponseData(parent::HTTP_OK, '更新成功'), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_OK, '更新失败'), parent::HTTP_OK);
        } 


    }


}
