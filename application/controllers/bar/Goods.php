<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Goods extends Admin_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('goods_model','goods');
        $this->load->model('good_type_model','good_type');

    }

    public function index(){
        $page = $this->input->get('page');
        $page = $page ? $page : 1;
        $data['page'] = $page;


        $num = 10;
        $offset = ($page - 1)*$num;

        $data['data'] = $this->goods->get_list(1,$num,$offset);

        $type = $this->good_type->get_list(-1);
        $data['type'] = array_column($type, 'name' ,'id');

        $good_num = $this->goods->get_num()->num;
        $data['page_count'] = ceil($good_num/$num);
        
        $this->load->view('goods/list',$data);
    }

	public function insert()
	{
        $action = $this->input->get('action');
        if($action == 'insert'){
            $parm = $_POST;
            unset($parm['submit']);
            $this->goods->insert($parm);

            redirect(ADMIN_PATH.'/goods');
            exit();
        }

        $type = $this->good_type->get_list();
        $data['type'] = array_column($type, 'name' ,'id');
        
		$this->load->view('goods/insert',$data);
	}

    public function update($id){
        $action = $this->input->get('action');
        if($action == 'update'){
            $parm = $_POST;
            unset($parm['submit']);
            $this->goods->update($id,$parm);
            redirect(ADMIN_PATH.'/goods');
            exit();
        }
        
        $type = $this->good_type->get_list();
        $data['type'] = array_column($type, 'name' ,'id');
        
        $data['data'] = $this->goods->get_info($id);
        $this->load->view('goods/update',$data);

    }

    public function delete($id){
        $this->machine->delete($id);

    }



}
