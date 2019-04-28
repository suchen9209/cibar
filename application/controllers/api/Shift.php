<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shift extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('Log_expense_model','log_expense');
        $this->load->model('Log_pay_model','log_pay');
        $this->load->model('Log_play_model','log_play');
        $this->load->model('Log_login_model','log_login');
        $this->load->model('order_status_model','order_status');
        $this->load->model('Shift_log_model','shift_log');
        
        $this->load->model('Machine_model','machine');
        $this->load->model('function/user_account_model','user_account');

    }

    public function last_info($type='query'){
        if($_SESSION['ouid']){
            $ouid = $_SESSION['ouid'];
        }else{
            $ouid = 0;
        }
        $starttime = $this->shift_log->get_last_time($ouid);
        if(!$starttime){$starttime = 0;}
        $endtime = time();

        //总充值额
        $pay_parm_in['time >'] = $starttime;
        $pay_parm_in['time <'] = $endtime;
        $order_parm_in['createtime >'] = $starttime;
        $order_parm_in['createtime <'] = $endtime;

        $pay_parm_in['pay_type'] = $this->config->item('log_pay_type')['meituan_white'];
        $total_money_pay_meituan_white = $this->log_pay->get_total_money($pay_parm_in);        
        $order_parm_in['payment'] = $this->config->item('log_pay_type')['meituan_white'];
        $total_money_order_meituan_white = $this->order_status->get_total_money($order_parm_in);
        $return['美团小白盒支付金额'] = $total_money_pay_meituan_white + $total_money_order_meituan_white;

        $pay_parm_in['pay_type'] = $this->config->item('log_pay_type')['meituan_pos'];
        $total_money_pay_meituan_pos = $this->log_pay->get_total_money($pay_parm_in);        
        $order_parm_in['payment'] = $this->config->item('log_pay_type')['meituan_pos'];
        $total_money_order_meituan_pos = $this->order_status->get_total_money($order_parm_in);
        $return['美团POS机支付金额'] = $total_money_pay_meituan_pos + $total_money_order_meituan_pos;

        $pay_parm_in['pay_type'] = $this->config->item('log_pay_type')['cash'];
        $total_money_pay_meituan_cash = $this->log_pay->get_total_money($pay_parm_in);        
        $order_parm_in['payment'] = $this->config->item('log_pay_type')['cash'];
        $total_money_order_meituan_cash = $this->order_status->get_total_money($order_parm_in);
        $return['现金支付金额'] = $total_money_pay_meituan_cash + $total_money_order_meituan_cash;

        $pay_parm_in['pay_type'] = $this->config->item('log_pay_type')['wx'];
        $total_money_pay_wx = $this->log_pay->get_total_money($pay_parm_in);  
        $return['微信小程序支付金额'] = $total_money_pay_wx;

        if($type == 'query'){
            $this->response($this->getResponseData(0, '统计', [$return]), parent::HTTP_OK);
        }else if($type == 'insert'){
            $insert_parm = array();
            $insert_parm['ouid'] = $ouid;
            $insert_parm['time'] = $endtime;
            $insert_parm['meituan_white'] = $return['美团小白盒支付金额'];
            $insert_parm['meituan_pos'] = $return['美团POS机支付金额'];
            $insert_parm['crash'] = $return['现金支付金额'];

            if($this->shift_log->insert($insert_parm)){
                $this->response($this->getResponseData(200, '交接成功'), parent::HTTP_OK);
            }else{
                $this->response($this->getResponseData(400, '交接失败，请重试'), parent::HTTP_OK);
            }
        }

        
    }


}
