<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('machine_model','machine');
        $this->load->model('machine_info_model','machine_info');
        $this->load->model('active_status_model','active_status');

    }

    public function index(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;

        $data = $this->machine->get_machine_list($page,$num);
        foreach ($data as $key => $value) {
            $data[$key]['type_id'] = $value['type'];
            $data[$key]['type'] = $this->config->item('machine_type')[$value['type']];
            $data[$key]['status_id'] = $value['status'];
            $data[$key]['status'] = $this->config->item('machine_hardware_status')[$value['status']];

        }

        $count = $this->machine->get_num();

        $this->response($this->getLayuiList(0,'机器列表',$count,$data));    
    }

    public function config_info(){

        $return_arr['status_list'] = $this->config->item('machine_hardware_status');
        $machine_type = $this->config->item('machine_type');
        $return = array();
        foreach ($machine_type as $key => $value) {
            $return []= array('id'=>$key,'name'=>$value);
        }
        $return_arr['type_list'] = $return;

        $this->response($this->getResponseData(parent::HTTP_OK, '机器类型和硬件状态',$return_arr), parent::HTTP_OK);
    }

	public function insert()
	{
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"machine_name":"S0088","ip":"158.222.666.33","type":"1","status":"1","position":"38","box_id":"散座"}';*/
        $data = json_decode($data_json,true);
        
        if($data){
            $this->db->trans_start();
            $machine_id = $this->machine->insert($data);

            $info_parm = array('machine_id'=>$machine_id,'machine_info'=>'预留位置','ip'=>$data['ip'],'mac'=>'mac地址');
            $this->machine_info->insert($info_parm);

            $active_parm = array('mid'=>$machine_id,'state'=>1);
            $this->active_status->insert($active_parm);

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();   //增加失败，回滚
                $this->response($this->getResponseData(parent::HTTP_OK, '增加失败'), parent::HTTP_OK);
            }else{
                $this->db->trans_complete();
                $this->response($this->getResponseData(parent::HTTP_OK, '增加成功'), parent::HTTP_OK);
            }
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '不能传空值'), parent::HTTP_OK);
        }
	}

    public function update($id){
        $data_json = $this->input->post_get('data');
/*        $data_json = '{"machine_name":"S0088","ip":"158.222.666.33","type":"1","status":"1","position":"38","box_id":"散座"}';*/
        $data = json_decode($data_json,true);
        if($data){
            $this->db->trans_start();
            $this->machine->update($id,$data);

            if($data['status'] == 2){
                $active_parm = array('state'=>4);//机器状态中标记为损坏
                $this->active_status->update($id,$active_parm);    
            }else if($data['status'] == 1){
                if($this->active_status->get_info($id)->state == 4){//将损坏的机器更为正常
                    $active_parm = array('state'=>1);
                    $this->active_status->update($id,$active_parm);  
                }
            }            

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();   //增加失败，回滚
                $this->response($this->getResponseData(parent::HTTP_OK, '更新失败'), parent::HTTP_OK);
            }else{
                $this->db->trans_complete();
                $this->response($this->getResponseData(parent::HTTP_OK, '更新成功'), parent::HTTP_OK);
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
