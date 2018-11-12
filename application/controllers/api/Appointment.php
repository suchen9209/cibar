<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Appointment extends Admin_Api_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('appointment_model','appointment');

    }

    public function index(){
        $list = $this->appointment->get_appoint_today();
        $this->response($this->getResponseData(parent::HTTP_OK, '今日预约', $list), parent::HTTP_OK);

    }



}
