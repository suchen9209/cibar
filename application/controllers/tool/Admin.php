<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('adminuser_model','adminuser');

    }

    public function index(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $offset = ($page - 1) * $num;

        $list = $this->adminuser->get_list($offset,$num);
        $count = $this->adminuser->get_num();

        $this->response($this->getLayuiList(0,'员工列表',$count,$list));
    }

    public function admin_role_info(){
        $role_list = $this->config->item('admin_role');
        foreach ($role_list as $key => $value) {
            $data['role_list'] []= array('authority'=>$key,'role'=>$value);
        }
    
        $this->response($this->getResponseData(parent::HTTP_OK, '身份列表',$data), parent::HTTP_OK);
    }

    public function insert(){
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"username":"百事可乐","password":"123456","phone":"15588887777","name":"噶尔","authority":1,"role":"店员"}';*/
        $data = json_decode($data_json,true);
        $data['password'] = password_md5($data['password']);
        
        if($data){
            if($this->adminuser->insert($data)){
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
        $data = json_decode($data_json,true);
        if($data && $id > 0){
            if($this->adminuser->update($id,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }


}
