<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine extends Admin_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('machine_model','machine');
        $this->load->model('machine_info_model','machine_info');
        $this->load->model('active_status_model','active_status');

    }

    public function index(){
        $page = $this->input->get('page');
        $page = $page ? $page : 1;
        $num = 20;
        $data['data'] = $this->machine->get_machine_list($page,$num);

        $count = $this->machine->get_num();
        $data['page_count'] = ceil($count/$num);
        
        $this->load->view('machine/list',$data);
    }

	public function insert()
	{
        $action = $this->input->get('action');
        if($action == 'insert'){
            $parm = $_POST;
            unset($parm['submit']);

            $this->db->trans_start();
            $machine_id = $this->machine->insert($parm);

            $info_parm = array('machine_id'=>$machine_id,'machine_info'=>'预留位置','ip'=>$parm['ip'],'mac'=>'mac地址');
            $this->machine_info->insert($info_parm);

            $active_parm = array('mid'=>$machine_id,'state'=>1);
            $this->active_status->insert($active_parm);

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();   //增加失败，回滚
            }else{
                $this->db->trans_complete();
            }

            redirect(ADMIN_PATH.'/machine');
            exit();
        }
        
		$this->load->view('machine/insert');
	}

    public function update($id){
        $action = $this->input->get('action');
        if($action == 'update'){
            $parm = $_POST;
            unset($parm['submit']);
            $this->machine->update($id,$parm);
            redirect(ADMIN_PATH.'/machine');
            exit();
        }
        
        $data['data'] = $this->machine->get_info($id);
        $this->load->view('machine/update',$data);

    }

    public function delete($id){
        $this->machine->delete($id);

    }



}
