<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends Admin_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('machine_model','machine');

    }

    public function index(){
        $page = $this->input->get('page');
        $page = $page ? $page : 1;
        $num = 20;
        $data['data'] = $this->machine->get_machine_list($page,$num);
        
        $this->load->view('machine/list',$data);
    }

	public function insert()
	{
        $action = $this->input->get('action');
        if($action == 'insert'){
            $parm = $_POST;
            unset($parm['submit']);
            $this->machine->insert($parm);
            redirect(MACHINE_PATH.'/main');
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
            redirect(MACHINE_PATH.'/main');
            exit();
        }
        
        $data['data'] = $this->machine->get_info($id);
        $this->load->view('machine/update',$data);

    }

    public function delete($id){
        $this->machine->delete($id);

    }



}
