<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weixin extends CI_Controller {

    private $app_id = 'wxe4ceea6213fa3b27'; 
    private $app_secret = '59c5ea8cbb77d923e09e036040d1b6e6';
    //private $callback_url = HTTP_OR_HTTPS.'www.imbatv.cn/qqlogin/weixin/callback';


    //private $wx_code_url = '';
    //private $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    private $get_id_url = "https://api.weixin.qq.com/sns/jscode2session";

    //https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID
    //private $wx_info_url = 'https://api.weixin.qq.com/sns/userinfo';
    private $code = '';

    public function __construct(){
        define("CLASS_PATH",dirname(__FILE__)."/API/class/");
        require_once(CLASS_PATH.'URL.class.php');
        parent::__construct();

    }

    public function index(){

        $this->load->model('user_model','user');
        $this->load->model('function/user_account_model','user_account_model');

        $this->code = $_GET['code'];
        $http = new URL();
        $parm['appid'] = $this->app_id;
        $parm['secret'] = $this->app_secret;
        $parm['js_code'] = $this->code;
        $parm['grant_type'] = 'authorization_code';
        $res = $http->get($this->get_id_url,$parm);
        $res_arr = json_decode($res,true);

        $return = [];

        $return['errcode'] = $res_arr['errcode'];
        $return['errmsg'] = $res_arr['errmsg'];
        if($res_arr['errcode'] == 0){
            //注册 or 登录
            $parm['openid'] = $res_arr['openid'];
            $parm['unionid'] = $res_arr['openid'];
            
            if($this->user->check_user()){
                $uid = $this->user_account_model->register('wx',$parm);
            }else{
                $uid = $this->user_account_model->login('wx',$parm);
            }
            $uid = 0;
            $return['openid'] = $return['openid'];
            $return['uid'] = $uid;
        }

        header('Content-Type:application/json');

        echo json_encode($return);
    
    }






}