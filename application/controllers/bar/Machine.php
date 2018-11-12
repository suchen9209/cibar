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

    public function insert_test(){
        for ($i=1; $i <= 18; $i++) { 
            $parm['machine_name'] = 'S'.str_pad($i,3,"0",STR_PAD_LEFT);
            $parm['ip'] = '192.168.15.'.$i;
            $parm['type'] = 1;
            $parm['position'] = $i;
            $parm['box_id'] = '散座';
            
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
        }

        $num = 1;
        for ($i=1; $i <= 30; $i++) { 
            for ($k=1; $k <= 5; $k++) { 
                $parm['machine_name'] = 'FB'.str_pad($num,3,"0",STR_PAD_LEFT);
                $parm['ip'] = '192.168.222.'.$num;
                $parm['type'] = 2;
                $parm['position'] = $k;
                $parm['box_id'] = '5人包厢'.$i;
                
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
                $num ++;
            }
            
        }

        $num = 1;
        for ($i=1; $i <= 3; $i++) { 
            for ($k=1; $k <= 6; $k++) { 
                $parm['machine_name'] = 'SB'.str_pad($num,3,"0",STR_PAD_LEFT);
                $parm['ip'] = '192.168.244.'.$num;
                $parm['type'] = 4;
                $parm['position'] = $k;
                $parm['box_id'] = '6人包厢'.$i;
                
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
                $num ++;
            }
            
        }

        $num = 1;
        for ($i=1; $i <= 2; $i++) { 
            for ($k=1; $k <= 10; $k++) { 
                $parm['machine_name'] = 'TB'.str_pad($num,3,"0",STR_PAD_LEFT);
                $parm['ip'] = '192.168.55.'.$num;
                $parm['type'] = 4;
                $parm['position'] = $k;
                $parm['box_id'] = '10人包厢'.$i;
                
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
                $num ++;
            }
            
        }

        for ($k=1; $k <= 20; $k++) { 
            $parm['machine_name'] = 'TTB'.str_pad($k,3,"0",STR_PAD_LEFT);
            $parm['ip'] = '192.168.22.'.$k;
            $parm['type'] = 4;
            $parm['position'] = $k;
            $parm['box_id'] = '20人包厢';
            
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
            $num ++;
        }
    }



}
