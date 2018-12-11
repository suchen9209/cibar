<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('Log_expense_model','log_expense');
        $this->load->model('Log_pay_model','log_pay');
        $this->load->model('Log_play_model','log_play');
        $this->load->model('function/user_account_model','user_account');

    }

    public function play(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $startdate = $this->input->get_post('startdate') ? $this->input->get_post('startdate') : date('Y-m-d',time()-24*3600);
        $enddate = $this->input->get_post('enddate') ? $this->input->get_post('enddate') : date('Y-m-d');
        $offset = ($page-1)*$num;

        $parm = array();
        $parm['log_play.endtime >'] = strtotime($startdate);
        $parm['log_play.endtime <'] = strtotime($enddate);

        $data = $this->log_play->get_list($offset,$num,$parm);
        foreach ($data as $key => $value) {
            $data[$key]['level'] = $this->user_account->get_member_level($value['uid']);
        }

        $count = $this->log_expense->get_num($parm);

        $this->response($this->getLayuiList(0,'上机款',$count,$data));    
    }

    public function sale(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $startdate = $this->input->get_post('startdate') ? $this->input->get_post('startdate') : date('Y-m-d',time()-24*3600);
        $enddate = $this->input->get_post('enddate') ? $this->input->get_post('enddate') : date('Y-m-d');
        $offset = ($page-1)*$num;

        $parm = array();
        $parm['log_expense.endtime >'] = strtotime($startdate);
        $parm['log_expense.endtime <'] = strtotime($enddate);
        
        $data = $this->log_expense->get_tool_list($offset,$num,$parm);
        foreach ($data as $key => $value) {
            $data[$key]['level'] = $this->user_account->get_member_level($value['uid']);
        }

        $count = $this->log_expense->get_num($parm);

        $this->response($this->getLayuiList(0,'销售款',$count,$data));            
    }

    public function pay(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $startdate = $this->input->get_post('startdate') ? $this->input->get_post('startdate') : date('Y-m-d',time()-24*3600);
        $enddate = $this->input->get_post('enddate') ? $this->input->get_post('enddate') : date('Y-m-d');
        $offset = ($page-1)*$num;

        $parm = array();
        $parm['log_pay.time >'] = strtotime($startdate);
        $parm['log_pay.time <'] = strtotime($enddate);

        $data = $this->log_pay->get_tool_list($offset,$num,$parm);
        foreach ($data as $key => $value) {
            $data[$key]['pay_type'] = $this->config->item('log_pay_type_cn')[$value['pay_type']];
            $data[$key]['level'] = $this->user_account->get_member_level($value['uid']);
        }

        $count = $this->log_pay->get_num($parm);

        $this->response($this->getLayuiList(0,'充值款',$count,$data));        
    }



}
