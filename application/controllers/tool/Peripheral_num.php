<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peripheral_num extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('peripheral_num_model','peripheral_num');

    }

    public function index(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $offset = ($page-1)*$num;

        $data = $this->peripheral_num->get_list($num,$offset);
        foreach ($data as $key => $value) {
            $data[$key]['type'] = $this->config->item('peripheral_type')[$value['type']];
        }

        $count = $this->peripheral_num->get_count();

        $this->response($this->getLayuiList(0,'硬件数目列表',$count,$data));    
    }

    public function config_info(){

        $return_arr['type_list'] = $this->config->item('peripheral_type');

        $this->response($this->getResponseData(parent::HTTP_OK, '硬件类型',$return_arr), parent::HTTP_OK);
    }

	public function insert()
	{
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"desc":"罗技 G102","count":"100","type":"1","total":"100"}';*/
        $data = json_decode($data_json,true);
        if($data){
            if($this->peripheral_num->insert($data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '增加成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '增加失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
	}

    public function update($id){
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"desc":"罗技 G102","count":"100","type":"1","total":"100"}';*/
        $data = json_decode($data_json,true);
        if($data){
            if($this->peripheral_num->update($id,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '增加成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '增加失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }

    }

    public function delete($id){
        if($this->machine->delete($id)){
            $this->response($this->getResponseData(parent::HTTP_OK, '删除成功'), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_OK, '删除失败'), parent::HTTP_OK);  
        }

    }


}
