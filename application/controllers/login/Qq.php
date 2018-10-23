<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Qq extends ImbaTV_Controller {
    public function __construct(){
        parent::__construct();
    	require_once(dirname(__FILE__)."/API/qqConnectAPI.php");
        $this->load->model('common/user_login_model', 'user_login');  
        
    }

    public function index(){
        $qc = new QC();
        $qc->qq_login();   
    }

    public function login(){
        $qc = new QC();
        $qc->qq_login();
    }


    public function callback(){
    	$qc = new QC();
        $access_token = $qc->qq_callback();
        $opend_id = $qc->get_openid();
        $union_id = $qc->get_unionid();

        $user = $this->user_login->check_user_exist('qq',$opend_id,$union_id);
        if($user){//已注册
            $this->user_login->user_login_multi('qq',$user,$redirect_url);
        }else{//未注册

            $qc = new QC($access_token,$opend_id);
            $user_info = $qc->get_user_info();
            if($user_info['ret'] < 0){
                $user_info['nickname'] = 'QQ用户'.$opend_id;
                $user_info['figureurl_qq_2'] = '';
            }

            $id_arr[0] =$opend_id;
            $id_arr[1] =$union_id;
            $this->user_login->user_register_multi('qq',$id_arr,$user_info);
        }
    }





}