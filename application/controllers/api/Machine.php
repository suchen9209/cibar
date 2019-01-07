<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();

        $this->load->model('machine_model','machine');
        $this->load->model('machine_info_model','machine_info');
        $this->load->model('active_status_model','active_status');
        $this->load->model('box_status_model','box_status');
        $this->load->model('log_login_model','log_login');
        $this->load->model('log_expense_model','log_expense');
        $this->load->model('log_play_model','log_play');
        $this->load->model('log_deduct_money_model','log_deduct_money');
        $this->load->model('peripheral_num_model','peripheral_num');
        $this->load->model('peripheral_last_model','peripheral_last');
        $this->load->model('function/send_wokerman_model','send_wokerman');
        $this->load->model('function/user_account_model','user_account');
        $this->load->model('user_model','user');
        $this->load->model('user_coupon_model','user_coupon');
        $this->load->model('account_model','account');
        $this->load->model('coupon_model','coupon');

    }

    public function index(){

        $type = $this->input->get_post('type');
        $data['machine_type'] =$this->config->item('machine_type');
        if($type){
            $data['machines'] =$this->machine->get_active_machine($type);
        }else{
            
            $data['machines'] = $this->machine->get_active_machine();  
        }
        $this->response($this->getResponseData(parent::HTTP_OK, '机器剩余情况', $data), parent::HTTP_OK);

    }

    public function all(){
        $type = $this->input->get_post('type');
        $data['machine_type'] =$this->config->item('machine_type');

        $data['machines'] =$this->machine->get_all_machine(array('machine.status'=>1));

        $this->response($this->getResponseData(parent::HTTP_OK, '所有机器', $data), parent::HTTP_OK);
    }

    public function order(){
        $uid = $this->input->get_post('user_id');
        $machine_id = $this->input->post_get('machine_id');

        if(isset($uid) && isset($machine_id) && $uid>0){

            if(!$this->active_status->get_info_uid($uid)){
                $log_parm['uid'] = $uid;
                $log_parm['login_type'] = $this->config->item('log_login_type')['bar'];
                $log_parm['machine_id'] = $machine_id;
                $log_parm['time'] = time();

                $this->db->trans_start();
                $this->log_login->insert($log_parm);
                $active_parm['uid'] = $uid;
                $active_parm['state'] = 2;
                $active_parm['updatetime'] = time();
                $this->active_status->update($machine_id,$active_parm);


                if($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $this->response($this->getResponseData(parent::HTTP_OK, '失败'), parent::HTTP_OK);
                }else{
                    $this->db->trans_complete();
                    //发送开机指令
                    $this->response($this->getResponseData(parent::HTTP_OK, '登记成功'), parent::HTTP_OK);
                }  
            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '该用户为上机状态，不可重复上机'), parent::HTTP_OK);
            }
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }
    }

    public function down_info(){
        $uid = $this->input->get_post('user_id');
        if(isset($uid) && $uid > 0){
            $log_deduct_info = $this->log_deduct_money->get_total_info($uid);
            if($log_deduct_info){

                $ac_temp = $this->active_status->get_info_uid($uid);
                $machine_id = $ac_temp->mid;
                $machine_info = $this->machine->get_info($machine_id);

                $return_data = array();
                $return_data['machine_info'] = $machine_info;
                $return_data['user_info'] = $this->user_account->get_user_info($uid);
                $return_data['deduct_info'] = $log_deduct_info;
                $return_data['deduct_info']['pay_user_info'] = $this->user_account->get_user_info($log_deduct_info['whopay']);
                $return_data['coupon_info'] = $this->user_coupon->get_can_use_by_uid_type($uid,1);

                $this->response($this->getResponseData(parent::HTTP_OK, '用户机器信息及扣款信息', $return_data), parent::HTTP_OK); 
            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '无记录', 'nothing'), parent::HTTP_OK);    
            }
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK); 
        }
    }

    public function down(){
        $uid = $this->input->get_post('user_id');
        $op = $this->input->get_post('op');
        $cid = $this->input->get_post('coupon_id')?$this->input->get_post('coupon_id'):0;

        if(isset($uid) && $uid > 0){
            $ac_temp = $this->active_status->get_info_uid($uid);
            $machine_id = $ac_temp->mid;
            $machine_info = $this->machine->get_info($machine_id);
            if($op == 'get'){
                $this->response($this->getResponseData(parent::HTTP_OK, 'sucess',$machine_info), parent::HTTP_OK);
            }else if($op == 'down'){
                $this->db->trans_start();

                //记录登出信息
                $log_parm['logout_time'] = time();           
                $login_info = $this->log_login->get_last_login_info($uid);   
                $this->log_login->update($login_info->id,$log_parm);

                //更新机器状态
                $active_parm['uid'] = 0;
                $active_parm['state'] = 1;
                $active_parm['updatetime'] = time();
                $this->active_status->update($machine_id,$active_parm);

                //更新外设库存
                if($last_p = $this->peripheral_last->get_last_by_uid($uid)){
                    $pjson = $last_p->pid;
                    $pdata = json_decode($pjson,true);
                    foreach ($pdata as $key => $value) {
                        $this->peripheral_num->in($value['id']);
                    }
                }

                //删除box_status中对应uid的记录
                if($this->box_status->get_info_uid($uid)){
                    $this->box_status->delete_by_uid($uid);
                }
                //记录本次上网log_deduct_money总值，计入log_expense表
                $deduct_info = $this->log_deduct_money->get_total_info($uid);
                //删除log_deduct_money中对应uid的记录
                $this->log_deduct_money->delete_by_uid($uid);

                //计入消费信息，并扣款
                if($deduct_info){     

                    if($cid > 0){//使用优惠券
                        $coupon_info = $this->coupon->get_info($cid);
                        $reduced_time = $coupon_info->num * 3600;
                        if($reduced_time > $deduct_info['total_time']){
                            $final_money = round($deduct_info['total_money'] * $coupon_info->discount,2);
                        }else{
                            // final_money = m*r/t*discount + m*(t-r)/t
                            $final_money = round($deduct_info['total_money'] * $reduced_time / $deduct_info['total_money'] * $coupon_info->discount , 2) + round($deduct_info['total_money'] * ($deduct_info['total_time'] - $reduced_time) / $deduct_info['total_time'] , 2);
                        }
                        $this->account->expense($deduct_info['whopay'],$final_money); 
                    }else{
                        $this->account->expense($deduct_info['whopay'],$deduct_info['total_money']);   
                    }            
                    

                    $log_play_parm = array();
                    $log_play_parm['uid'] = $deduct_info['whopay'];
                    $log_play_parm['starttime'] = strtotime($deduct_info['start_time']);
                    $log_play_parm['endtime'] = strtotime($deduct_info['end_time']);
                    $log_play_parm['number'] = round(($log_play_parm['endtime'] - $log_play_parm['starttime'])/3600 , 2);
                    $log_play_parm['price'] = $this->config->item('price')[$machine_info->type];
                    $log_play_parm['money'] = $deduct_info['total_money'];
                    if($deduct_info['whopay'] != $uid){
                        $log_play_parm['extra'] = '请客，为'.$this->user->get_user_info($uid)->username.'买单';  
                    }

                    $this->log_play->insert($log_play_parm);    
                }
                      
 
                if($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $this->response($this->getResponseData(parent::HTTP_OK, '失败'), parent::HTTP_OK);
                }else{
                    $this->db->trans_complete();
                    //发送关机指令
                    $send_parm = array();
                    $send_parm['uid'] = $uid;
                    $send_parm['mid'] = $machine_id;
                    $send_parm['cmd'] = 'down';
                    $this->send_wokerman->send(json_encode($send_parm));
                    $this->response($this->getResponseData(parent::HTTP_OK, '已下机'), parent::HTTP_OK);
                }

            }else{
                $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
            }
        }else{
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '参数错误', 'nothing'), parent::HTTP_OK);
        }
        
    }


}
