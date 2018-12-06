<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__).'/Weixin.php';

class Wxpay extends Weixin {
    public $mch_id = '1519885631';//商户ID

    public $request_url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    public $notify_url = 'https://pay.imbatv.cn/login/wxpay/back';

    public $key = 'ARv2KbTTrIU6H4TAMtSGY1CNeszQwZhP';

    public function __construct(){
        parent::__construct();
        $this->load->model('log_wx_pay_model','log_wx_pay');
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

            $xml_parm = $this->arrayToXml($parm);

            $res_xml = $http->post($this->request_url,$xml_parm);

            $obj = simplexml_load_string($res_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $data = json_decode(json_encode($obj), true);

            if($data['return_code'] == 'SUCCESS'){
                $time =time();
                unset($parm['sign']);
                $parm['time'] = $time;
                $log_id = $this->log_wx_pay->insert($parm);

                $return['appId'] = $this->app_id;                
                $return['nonceStr'] = makeRandomSessionName(10);
                $return['package'] = 'prepay_id='.$data['prepay_id'];
                $return['signType'] = 'MD5';
                $return['timeStamp'] = $time;

                $paySign = md5(implode("&", $return));
                $return['paySign'] = $paySign;
                $return['return_code'] = 'SUCCESS';
                $return['return_msg'] = 'OK';

                header('Content-Type:application/json');
                echo json_encode($return); 

            }else{
                header('Content-Type:application/json');
                echo json_encode($data); 
            }
        }else{
            $return['return_code'] = 'ERROR';
            $return['return_msg'] = '参数错误';

            header('Content-Type:application/json');

            echo json_encode($return);
        }
    
    }

    public function back(){

    }

    public function arrayToXml($data){
        if(!is_array($data) || count($data) <= 0){
            return false;
        }
        $xml = "<xml>";
        foreach ($data as $key=>$val){
        if (is_numeric($val)){
            $xml.="<".$key.">".$val."</".$key.">";
        }else{
          $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        }
        $xml.="</xml>";
        return $xml;
    }

}