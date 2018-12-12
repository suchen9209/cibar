<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('Log_expense_model','log_expense');
        $this->load->model('Log_pay_model','log_pay');
        $this->load->model('Log_play_model','log_play');
        $this->load->model('Log_login_model','log_login');
        
        $this->load->model('Machine_model','machine');
        $this->load->model('function/user_account_model','user_account');

    }

    public function play(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $startdate = $this->input->get_post('startdate') ? $this->input->get_post('startdate') : date('Y-m-d',time()-24*3600);
        $enddate = $this->input->get_post('enddate') ? $this->input->get_post('enddate').' 23:59:59' : date('Y-m-d H:i:s');
        $offset = ($page-1)*$num;

        $parm = array();
        $parm['log_play.endtime >'] = strtotime($startdate);
        $parm['log_play.endtime <'] = strtotime($enddate);


        $data = $this->log_play->get_list($offset,$num,$parm);
        foreach ($data as $key => $value) {
            $data[$key]['level'] = $this->user_account->get_member_level($value['uid']);
        }

        $count = $this->log_play->get_num($parm);

        $this->response($this->getLayuiList(0,'上机款',$count,$data));    
    }

    public function sale(){
        $page = $this->input->get_post('page') ? $this->input->get_post('page') : 1;
        $num = $this->input->get_post('limit') ? $this->input->get_post('limit') : 20;
        $startdate = $this->input->get_post('startdate') ? $this->input->get_post('startdate') : date('Y-m-d',time()-24*3600);
        $enddate = $this->input->get_post('enddate') ? $this->input->get_post('enddate').' 23:59:59' : date('Y-m-d H:i:s');
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
        $enddate = $this->input->get_post('enddate') ? $this->input->get_post('enddate').' 23:59:59' : date('Y-m-d H:i:s');
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

    public function report(){
        $startdate = $this->input->get_post('startdate') ? $this->input->get_post('startdate') : date('Y-m-d',time()-24*3600);
        $enddate = $this->input->get_post('enddate') ? $this->input->get_post('enddate').' 23:59:59' : date('Y-m-d H:i:s');

        $return = array();

        $log_parm['endtime >'] = strtotime($startdate);
        $log_parm['endtime <'] = strtotime($enddate);
        //饮料食品等消费额
        $total_money_expense = $this->log_expense->get_total_money($log_parm);
        //上机消费额
        $total_money_play = $this->log_play->get_total_money($log_parm);
        if(!$total_money_play){
            $total_money_play = 0;
        }
        //总充值额
        $pay_parm['time >'] = strtotime($startdate);
        $pay_parm['time <'] = strtotime($enddate);
        $total_money_pay = $this->log_pay->get_total_money($pay_parm);
        //总机器数
        $machine_num = $this->machine->get_num(array('status'=>1));
        $machine_num_all = $this->machine->get_num();

        //总营业额
        $turnover = $total_money_expense + $total_money_play;
        //总单机
        $return['总单机'] = round($turnover/$machine_num,2);

        //上座率        
        $all_active_data = round( (strtotime($enddate) - strtotime($startdate)) * $machine_num / 60 , 2);//满座的话，总上机时长  分钟级
        $use_active_data = round($this->log_login->get_total_time( strtotime($startdate) ,strtotime($enddate) ) / 60 ,2);//实际总上机时长
        $return['上座率'] = round( $use_active_data / $all_active_data * 100 , 2 ).'%';

        //上网均价
        if($use_active_data == 0){
            $return['上网均价'] = 0;
        }else{
            $return['上网均价'] = round( $total_money_play / ceil($use_active_data / 3600) ,2);
        }

        $return['上网收入'] = $total_money_play;
        $return['上网占比'] = round( $total_money_play / $turnover * 100 , 2).'%';

        //饮料收入
        $drink_parm['endtime >'] = strtotime($startdate);
        $drink_parm['endtime <'] = strtotime($enddate);
        $drink_parm['type'] = 1;
        $total_money_drink = $this->log_expense->get_total_money($drink_parm);
        $return['饮料收入'] = $total_money_drink;

        //休闲食品收入
        $food_parm['endtime >'] = strtotime($startdate);
        $food_parm['endtime <'] = strtotime($enddate);
        $food_parm['type'] = 2;
        $total_money_food = $this->log_expense->get_total_money($food_parm);
        $return['休闲食品收入'] = $total_money_food;

        $return['会员充值收入'] = $total_money_pay;
        $return['总营业额'] = $turnover;

        //人次，每机器每小时
        $people_num = $this->log_login->get_people_num( strtotime($startdate) ,strtotime($enddate) );
        $return['人次'] = $people_num;

        //卖出饮料数量
        $drink_num = $this->log_expense->get_drink_num($drink_parm);
        $return['饮料捕获率'] = round( $drink_num / $people_num * 100 ,2 ).'%';

        $return['翻机率'] = round( $people_num / $machine_num * 100 ,2 ).'%';

        $return['总客单价'] = round( $turnover / $people_num ,2 );  

        $return['人均上机时长'] = round($use_active_data / $people_num , 2);

        $new_member = $this->user->get_new_member( strtotime($startdate) ,strtotime($enddate) );
        $return['新办会员'] = $new_member;

        $return['PC机台数/可运营台数'] = $machine_num_all . '/' . $machine_num;

        $this->response($this->getResponseData(0, '统计', [$return]), parent::HTTP_OK);



        //总营业额

        //总单机
    }



}
