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
        $data['uid'] = $this->input->get_post('uid') ? $this->input->get_post('uid') : 0;
        $data['level'] = $this->input->get_post('level') ? $this->input->get_post('level') : 0;
        $data['days'] = $this->input->get_post('days') ? $this->input->get_post('days') : 0;

        $data['starttime'] = time();
        $data['endtime'] = time() + 3600*24*$data['days'];
        unset($data['days']);
        if($_SESSION['ouid']){
            $ouid = $_SESSION['ouid'];
        }else{
            $ouid = 0;
        }
        $data['ouid'] = $ouid;
        
        if($data['uid'] && $data['level']){
            $vip_id = $this->vip_level_special->insert($data);
            if($vip_id || $vip_id == 0){
                $this->response($this->getResponseData(parent::HTTP_OK, '增加成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '增加失败',$data), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }

    public function update($uid=0){

        $data['level'] = $this->input->get_post('level') ? $this->input->get_post('level') : 0;
        $data['endtime'] = $this->input->get_post('endtime') ? $this->input->get_post('endtime') : 0;
        if($data['level'] && $data['endtime'] && $uid > 0){
            if($this->vip_level_special->update($uid,$data)){
                $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
    }

    public function delete($uid=0){
        if($uid > 0){
            if($this->vip_level_special->delete_by_uid($uid)){
                $this->response($this->getResponseData(parent::HTTP_OK, '撤销成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(parent::HTTP_OK, '撤销失败'), parent::HTTP_OK);
            }  
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, 'uid不能为0'), parent::HTTP_OK);
        }
    }

    public function insert_or_update($uid=0){
        $vip_info = $this->vip_level_special->get_info_by_uid($uid);

        if($uid == 0){
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, 'uid不能为0'), parent::HTTP_OK);
        }

        if(isset($vip_info)){
            $data['level'] = $this->input->get_post('level') ? $this->input->get_post('level') : 0;
            $data['endtime'] = $this->input->get_post('endtime') ? $this->input->get_post('endtime') : 0;
            if($data['level'] && $data['endtime'] && $uid > 0){
                if($this->vip_level_special->update($uid,$data)){
                    $this->response($this->getResponseData(parent::HTTP_OK, '更改成功'), parent::HTTP_OK);
                }else{
                    $this->response($this->getResponseData(parent::HTTP_OK, '更改失败'), parent::HTTP_OK);
                }  
            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
            }   
        }else{
            $inset_parm = array();
            $inset_parm['uid'] = $uid;
            $inset_parm['level'] = $this->input->get_post('level') ? $this->input->get_post('level') : 0;
            $inset_parm['starttime'] = time();
            $inset_parm['endtime'] = $this->input->get_post('endtime') ? $this->input->get_post('endtime') : 0;
            if($_SESSION['ouid']){
                $ouid = $_SESSION['ouid'];
            }else{
                $ouid = 0;
            }
            $inset_parm['ouid'] = $ouid;
            
            if($inset_parm['uid'] && $inset_parm['level']){
                $vip_id = $this->vip_level_special->insert($inset_parm);
                if($vip_id || $vip_id == 0){
                    $this->response($this->getResponseData(parent::HTTP_OK, '增加成功'), parent::HTTP_OK);
                }else{
                    $this->response($this->getResponseData(parent::HTTP_OK, '增加失败',$data), parent::HTTP_OK);
                }  
            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
            }
        }
        
    }

    public function info($uid){
        $info = $this->vip_level_special->get_info_by_uid($uid);
        if($info){
            $this->response($this->getResponseData(parent::HTTP_OK, 'VIP信息',$info), parent::HTTP_OK);
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '无特殊VIP信息'), parent::HTTP_OK);
        }
    }




}
