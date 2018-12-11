<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Good_type extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('goods_model','goods');
        $this->load->model('good_type_model','good_type');

    }

    public function index(){
        $page = $this->input->get('page');
        $page = $page ? $page : 1;
        $num = 20;
        $data = $this->good_type->get_list(-1);

        $this->response($this->getResponseData(parent::HTTP_OK, '所有类型',$data), parent::HTTP_OK);
    }

	public function insert()
	{
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"name":"饮品",status":"1"}';*/
        $data = json_decode($data_json,true);
        
        if($data){
            if($this->good_type->insert($data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '增加成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '增加失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
	}

    public function good_type_info($id){
        $data['data'] = $this->good_type->get_info($id);
        $data['status_list'] = $this->config->item('status_common');
        $this->response($this->getResponseData(parent::HTTP_OK, '商品类型信息',$data), parent::HTTP_OK);
    }

    public function update($id=0){
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"name":"饮品",status":"1"}';*/
        $data = json_decode($data_json,true);
        
        if($data && $id>0){
            if($this->good_type->update($id,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更新成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更新失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }

    }

}
