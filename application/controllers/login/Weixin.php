<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weixin extends CI_Controller {

    public $app_id = 'wx6405c0270703e4b3'; 
    public $app_secret = '1b898cece00cf290125bd23459fb0160';
    //private $callback_url = HTTP_OR_HTTPS.'www.imbatv.cn/qqlogin/weixin/callback';


    //private $wx_code_url = '';
    //private $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    public $get_id_url = "https://api.weixin.qq.com/sns/jscode2session";

    //https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID
    //private $wx_info_url = 'https://api.weixin.qq.com/sns/userinfo';
    public $code = '';

    public function __construct(){
        define("CLASS_PATH",dirname(__FILE__)."/API/class/");
        require_once(CLASS_PATH.'URL.class.php');
        require_once(CLASS_PATH.'WXBizDataCrypt.php');
        parent::__construct();

        $this->load->model('user_model','user');
        $this->load->model('tmp_user_wx_model','tmp_user_wx');
        $this->load->model('function/user_account_model','user_account_model');

    }

    public function index(){

        

        $this->code = $_GET['code'];
        $http = new URL();
        $parm['appid'] = $this->app_id;
        $parm['secret'] = $this->app_secret;
        $parm['js_code'] = $this->code;
        $parm['grant_type'] = 'authorization_code';
        $res = $http->get($this->get_id_url,$parm);
        $res_arr = json_decode($res,true);

        $return = [];


/*            $res_arr['errcode'] = 0;
            $res_arr['openid'] = '22223sahfjkasdkjfhsakdhf';
            $res_arr['unionid'] = 'SDF4343KSDJFJKHSDHFKJDSNJKFHSD';*/


        if($res_arr['errcode'] == 0 || !isset($res_arr['errcode'])){
            //注册 or 登录
            $parm['openid'] = $res_arr['openid'];
            $parm['unionid'] = $res_arr['openid'];
            $parm['wxsessionkey'] = $res_arr['session_key'];
            
            if($this->user->check_user('wx',$parm)){
                $session_name = $this->user_account_model->login('wx',$parm);
            }else{
                $session_name = $this->user_account_model->register('wx',$parm);
            }

            $return['errcode'] = 0;
            $return['errmsg'] = 'no error';
            //$return['openid'] = $res_arr['openid'];
            $return['3rd_session'] = $session_name;
        }else{
            $return['errcode'] = $res_arr['errcode'];
            $return['errmsg'] = $res_arr['errmsg'];

        }

        header('Content-Type:application/json');

        echo json_encode($return);
    
    }


    public function bind_phone(){
        header('Content-Type:application/json');
        $encryptedData = $this->input->get_post('encryptedData');
        $iv = $this->input->get_post('iv');
        $mem_key = $this->input->get_post('3rd_session');



        $this->load->driver('cache');
        $tmp_uid = $this->cache->memcached->get($mem_key);

        $tmp_id = intval(str_replace("tmp","",$tmp_uid));

        $tmp_user_wx_info = $this->tmp_user_wx->get_info($tmp_id);
        $sessionKey = $tmp_user_wx_info->sessionkey;
        /*$this->user->get_user_info_by_phone();*/

        $pc = new WXBizDataCrypt($this->app_id, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            $data_arr = json_decode($data,true);
            $phone = $data_arr['phoneNumber'];

            $user = $this->user->get_info_u('phone',$phone);
            if($user){
                $update_parm['wxid'] = $tmp_user_wx_info->openid;
                $update_parm['wxunionid'] = $tmp_user_wx_info->unionid;
                $update_parm['wxsessionkey'] = $tmp_user_wx_info->sessionkey;

                $this->user->update($user->id,$update_parm)
                $return['errcode'] = 0;
                $return['errmsg'] = 'no error';
                $session_name = makeRandomSessionName(16);
                $this->save_info(array($session_name=>$user->id));
                $return['3rd_session'] = $session_name;

            }else{
                $return['errcode'] = 444;
                $return['errmsg'] = 'no user in this phone';
            }            
        } else {
            $return['errcode'] = $errCode;
            $return['errmsg'] = 'decrypt error';
            
        }

        $return['data'] = $tmp_user_wx_info;
        $return['uid'] = $user->id;
        $return['update_parm'] = $update_parm;
        echo json_encode($return);

    }

/*    public function bind_phone(){
        header('Content-Type:application/json');
        $encryptedData = $this->input->get_post('encryptedData');
        $iv = $this->input->get_post('iv');
        $mem_key = $this->input->get_post('3rd_session');



        $this->load->driver('cache');
        $uid = $this->cache->memcached->get($mem_key);
        $user_info = $this->user->get_user_info($uid);
        $sessionKey = $user_info->wxsessionkey;

        $pc = new WXBizDataCrypt($this->app_id, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            $data_arr = json_decode($data,true);
            $phone = $data_arr['phoneNumber'];
            if($this->user->update($uid,array('phone'=>$phone))){
                $return['errcode'] = 0;
                $return['errmsg'] = 'no error';
                echo json_encode($return);
            }else{
                $return['errcode'] = 500;
                $return['errmsg'] = 'update error';
                echo json_encode($return);
            }
        } else {
            $return['errcode'] = $errCode;
            $return['errmsg'] = 'decrypt error';
            echo json_encode($return);
        }

    }

*/



}