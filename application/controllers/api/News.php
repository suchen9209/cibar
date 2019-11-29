<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();
        //$session_name = 'user_id';
        $this->load->model('news_model','news');
    }

    public function index(){

        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $offset = ($page - 1) * $num;

        $data = $this->news->get_list($offset,$num);
        $count = intval($this->news->get_num());


        $this->response($this->getLayuiList(0,'活动列表',$count,$data));
    }

    public function insert(){
        $data_json = $this->input->post_get('data');
        //$data_json = '{"title":"测试12312","pic":"----------------","showdate":1545000555,content":"246546545646546","type":"1"}';
        $data = json_decode($data_json,true);
        
        if($data){
            $data['createdate'] = time();
            if($this->news->insert($data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '增加成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '增加失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }

    public function update($id=0){
        $data_json = $this->input->post_get('data');
        //$data_json = '{"title":"测试12312","pic":"----------------","content":"246546545646546","type":"1"}';
        $data = json_decode($data_json,true);
        if($data && $id > 0){
            if($this->news->update($id,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }


}
