<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment extends App_Api_Controller {

	public function __construct(){

		//$session_name = 'user_id';
		parent::__construct('app_rest');
		$this->load->model('function/user_account_model','user_account');
		$this->load->model('log_pay_model','log_pay');
		$this->load->model('appointment_model','appointment');
		$this->load->model('function/machine_model','machine');
	}

	public function index(){
		$uid = $this->getUserId();
		if($uid){
			$date = intval($this->input->post_get('date'));
			$type = $this->input->post_get('type');//散座还是包厢
			$number = $this->input->post_get('number');//人数
			$time = intval($this->input->post_get('date'));
			if(is_timestamp($date) && in_array($type, $this->config->item('seat_type')) && is_numeric($number)){
				//判断此人是否预约过
				if($this->appointment->get_apoint_near_date($uid,$date)){
					$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '已预约过相似时间段'), parent::HTTP_OK);
				}else{
					//判断时间段是否满了
					//对应选择的时间段和座位类型，当前的预约人数
					$appointment_num = $this->appointment->get_appointnum_in_date_and_number($date,$type,$number);

					//获取当前选择类型机器总数，后期补充,暂时写死
					$num = $this->machine->get_machine_number($type,$number);

					if($type == $this->config->item('seat_type')['seat']){
						$j = $number + $appointment_num;
					}else{
						$j = $appointment_num + 1;
					}

					if($j > $num){//满了，返回已预约，请选择其他
						$this->response($this->getResponseData(parent::HTTP_OK, '预约失败,该类型已满'), parent::HTTP_OK);
					}else{//没满，录入预约记录
						$appoint_parm['uid'] = $uid;
						$appoint_parm['createtime'] = time();
						$appoint_parm['state'] = $this->config->item('appointment_status')['indate'];
						$appoint_parm['starttime'] = $date;
						$appoint_parm['endtime'] = $date + 3600*$time;//默认预约6小时
						$appoint_parm['type'] = $type;
						$appoint_parm['number'] = $number;

						if($this->appointment->insert($appoint_parm)){
							$this->response($this->getResponseData(parent::HTTP_OK, '预约成功'), parent::HTTP_OK);
						}else{
							$this->response($this->getResponseData(parent::HTTP_OK, '预约失败，请联系服务员'), parent::HTTP_OK);
						}
					}			
				}
			}else{
				$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, 'error', '参数错误'), parent::HTTP_OK);
			}
		}else{
			$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, 'error', '登录信息失效'), parent::HTTP_OK);
		}

	}

	private function appoint_logic(){

	}

	//查看我未完成的预约
	public function appoint(){

	}

	//取消预约
	public function cancel(){
		$uid = $this->getUserId();
		if($uid){
			$appoint_id = $this->input->post_get('appoint_id');
			if($this->appointment->update($appoint_id,array('state'=>$this->config->item('appointment_status')['cancel']))){
				$this->response($this->getResponseData(parent::HTTP_OK, '取消成功'), parent::HTTP_OK);
			}else{
				$this->response($this->getResponseData(parent::HTTP_OK, '取消失败'), parent::HTTP_OK);
			}
		}else{
			$this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, 'error', '登录信息失效'), parent::HTTP_OK);
		}
	}

	//修改预约
	public function change(){

	}



}
