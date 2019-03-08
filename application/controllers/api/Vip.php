<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vip extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('vip_level_special_model','vip_level_special');

    }

    public function index(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $offset = ($page - 1) * $num;

        $data = $this->vip_level_special->get_list($offset,$num);
        $count = $this->vip_level_special->get_num();


        $this->response($this->getLayuiList(0,'特殊VIP列表',$count,$data));

    }

    public function insert(){
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"uid":52,"level":7,"days":365}';*/
        $data = json_decode($data_json,true);
        $data['starttime'] = time();
        $data['endtime'] = time() + 3600*24*$data['days'];
        if($_SESSION['ouid']){
            $ouid = $_SESSION['ouid'];
        }else{
            $ouid = 0;
        }
        $data['ouid'] = $ouid;
        
        if($data){
            if($this->vip_level_special->insert($data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '增加成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '增加失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }

    public function update($uid=0){
        $data_json = $this->input->post_get('data');
        $data = json_decode($data_json,true);
        if($data && $uid > 0){
            if($this->vip_level_special->update($uid,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }




}
