<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Good_type extends Admin_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('goods_model','goods');
        $this->load->model('good_type_model','good_type');

    }

    public function index(){
        $page = $this->input->get('page');
        $page = $page ? $page : 1;
        $num = 20;
        $data['data'] = $this->good_type->get_list();
        $data['data_un'] = $this->good_type->get_list(0);
        
        $this->load->view('good_type/list',$data);
    }

	public function insert()
	{
        $action = $this->input->get('action');
        if($action == 'insert'){
            $parm = $_POST;
            unset($parm['submit']);

            $good_type_id = $this->good_type->insert($parm);

            redirect(ADMIN_PATH.'/good_type');
            exit();
        }
        
		$this->load->view('good_type/insert');
	}

    public function update($id){
        $action = $this->input->get('action');
        if($action == 'update'){
            $parm = $_POST;
            unset($parm['submit']);
            $this->good_type->update($id,$parm);
            redirect(ADMIN_PATH.'/good_type');
            exit();
        }
        
        $data['data'] = $this->good_type->get_info($id);
        $this->load->view('good_type/update',$data);

    }

    public function delete($id){
        $this->good_type->delete($id);
    }



}
