<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weixin extends CI_Controller {

    private $app_id = 'wx849138f27a347b79'; 
    private $app_secret = '08c2edf4ea376b6ac13e4d8c4b5655eb';
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


/*        //测试
            $res_arr['errcode'] = 0;
            $res_arr['openid'] = 'sahfjkasdkjfhsakdhf';
            $res_arr['unionid'] = 'KSDJFJKHSDHFKJDSNJKFHSD';


        //测试*/
        if($res_arr['errcode'] == 0 || !isset($res_arr['errcode'])){
            //注册 or 登录
            $parm['openid'] = $res_arr['openid'];
            $parm['unionid'] = $res_arr['openid'];
            
            if($this->user->check_user('wx',$parm)){
                $uid = $this->user_account_model->login('wx',$parm);
            }else{
                $uid = $this->user_account_model->register('wx',$parm);
            }

            $return['errcode'] = 0;
            $return['errmsg'] = 'no error';
            $return['openid'] = $res_arr['openid'];
            $return['uid'] = $uid;
        }else{
            $return['errcode'] = $res_arr['errcode'];
            $return['errmsg'] = $res_arr['errmsg'];

        }

        header('Content-Type:application/json');

        echo json_encode($return);
    
    }






}