<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__).'/Weixin.php';

class Wxpay extends Weixin {
    public $mch_id = '1519885631';//商户ID

    public $request_url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    public $notify_url = 'https://pay.imbatv.cn/login/wxpay';

    public $key = 'ARv2KbTTrIU6H4TAMtSGY1CNeszQwZhP';

    public function __construct(){
        parent::__construct();
    }

    public function index(){


        $num = $this->input->post_get('number');
        $mem_key = $this->input->get_post('3rd_session');

        if($num && $mem_key){
            $this->load->driver('cache');
            $uid = $this->cache->memcached->get($mem_key);
            $user_info = $this->user->get_user_info($uid);
            $openid = $user_info->wxid;        

            $http = new URL();
            $parm['appid'] = $this->app_id;
            $parm['body'] = '会员充值';

            $parm['device_info'] = 'WEB';
            $parm['mch_id'] = $this->mch_id;
            $parm['nonce_str'] = makeRandomSessionName(10);
            $parm['notify_url'] = $this->notify_url;
            $parm['openid'] = $openid;
            $parm['out_trade_no'] = date('YmdHis').str_pad($uid,6,"0",STR_PAD_LEFT).rand(1,10000);
            $parm['spbill_create_ip'] = '139.198.188.162';
            $parm['total_fee'] = intval($num*100);
            $parm['trade_type'] = 'JSAPI';

            foreach ($parm as $key => $value) {
                $valueArr[] = "$key=$value";
            }
            $keyStr = implode("&",$valueArr);
            $keyStr .= "&key=".$this->key;

            $parm['sign'] = strtoupper(md5($keyStr));


            $res = $http->post($this->request_url,$parm);
            header('Content-Type:application/json');
            echo $res;die;
            $res_arr = json_decode($res,true);

            $return = [];

            var_dump($res_arr);          
        }else{
            echo 'error arg';
        }



/*            $res_arr['errcode'] = 0;
            $res_arr['openid'] = '22223sahfjkasdkjfhsakdhf';
            $res_arr['unionid'] = 'SDF4343KSDJFJKHSDHFKJDSNJKFHSD';*/
/*

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

        echo json_encode($return);*/
    
    }

}