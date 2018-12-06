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
        $this->load->model('log_pay_model','log_pay');
        $this->load->model('account_model','account');
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

            $parm['sign'] = $this->array_to_str_special($parm);

            $xml_parm = $this->arrayToXml($parm);

            $res_xml = $http->post($this->request_url,$xml_parm);

            $obj = simplexml_load_string($res_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $data = json_decode(json_encode($obj), true);

            if($data['return_code'] == 'SUCCESS'){
                $time =time();
                $parm['time'] = $time;
                $parm['uid'] = $uid;
                unset($parm['notify_url']);
                unset($parm['openid']);
                //计入记录，用于支付成功后验证
                $log_id = $this->log_wx_pay->insert($parm);

                if($log_id){
                    $return['appId'] = $this->app_id;                
                    $return['nonceStr'] = makeRandomSessionName(10);
                    $return['package'] = 'prepay_id='.$data['prepay_id'];
                    $return['signType'] = 'MD5';
                    $return['timeStamp'] = strval($time);
                    $return['paySign'] = $this->array_to_str_special($return);
                    $return['return_code'] = 'SUCCESS';
                    $return['return_msg'] = 'OK';

                    header('Content-Type:application/json');
                    echo json_encode($return);    
                }else{
                    $return['return_code'] = 'ERROR';
                    $return['return_msg'] = '服务器错误';
                    header('Content-Type:application/json');
                    echo json_encode($return); 
                }
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

        public function test(){

         $http = new URL();

        //$res_xml = $http->post('https://pay.imbatv.cn/login/wxpay/back',$this->array_to_str_special($arr));
$res_xml = $http->post('https://pay.imbatv.cn/login/wxpay/back','<xml><appid><![CDATA[wx6405c0270703e4b3]]></appid>
<bank_type><![CDATA[CFT]]></bank_type>
<cash_fee><![CDATA[1]]></cash_fee>
<device_info><![CDATA[WEB]]></device_info>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[N]]></is_subscribe>
<mch_id><![CDATA[1519885631]]></mch_id>
<nonce_str><![CDATA[rsI_lNnQJs]]></nonce_str>
<openid><![CDATA[oXjYN5HlTQUKaNHDUx_OjBkgX-EI]]></openid>
<out_trade_no><![CDATA[201812061812050000583844]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[5E134306C9AC38EE3C4F15F34EA72C81]]></sign>
<time_end><![CDATA[20181206181211]]></time_end>
<total_fee>1</total_fee>
<trade_type><![CDATA[JSAPI]]></trade_type>
<transaction_id><![CDATA[4200000214201812066602195436]]></transaction_id>
</xml>');

    echo 1;

        //header('Content-Type:application/xml');
    }

    public function back(){
        $xmldata = file_get_contents("php://input");
        var_dump($xmldata);
        $obj = simplexml_load_string($xmldata, 'SimpleXMLElement', LIBXML_NOCDATA);
        var_dump($obj);
        $data = json_decode(json_encode($obj), true);

        var_dump($data);die;

        file_put_contents(dirname(__FILE__).'/1.xml',$xmldata);
        file_put_contents(dirname(__FILE__).'/1.txt',$xmldata);
        die;

        if($data['return_code'] == 'SUCCESS'){

            //验证签名，保证数据为服务器传递过来的数据
            $receive_sign = $data['sign'];
            unset($data['sign']);
            foreach ($data as $key => $value) {
                if(!$value){
                    unset($data[$key]);
                }
            }
            $calculate_sign = $this->array_to_str_special($data);

            if($calculate_sign == $receive_sign){
                $log = $this->log_wx_pay->get_info_by_out_trade_no($data['out_trade_no']);   
                if($log && $log->total_fee == $data['total_fee'] && $log->state == 0){

                    $this->db->trans_start();

                    $this->log_wx_pay->update($log->id,array('state'=>1,'transaction_id'=>$data['transaction_id']));

                    $log_parm['uid'] = $log->uid;
                    $log_parm['time'] = time();
                    $log_parm['money'] = $log->total_fee/100;
                    $log_parm['pay_type'] = $this->config->item('log_pay_type')['wx'];
                    $log_parm['operator'] = $log->uid;
                    $this->log_pay->insert($parm);
                    $this->account->recharge($uid,$parm['money']);

                    if($this->db->trans_status() === FALSE){
                        $this->db->trans_rollback();
                        $return['return_code'] = 'FAIL';
                        $return['return_msg'] = 'mysql update error'; 
                    }else{
                        $this->db->trans_complete();
                        $return['return_code'] = 'SUCCESS';
                        $return['return_msg'] = 'OK'; 
                    }   
                }else{
                    $return['return_code'] = 'FAIL';
                    $return['return_msg'] = 'not same to log'; 
                }
            }else{
                $return['return_code'] = 'FAIL';
                $return['return_msg'] = 'sign not matching'; 
            }
        }else{
            $return['return_code'] = 'FAIL';
            $return['return_msg'] = 'error from weixin'; 
        }

        header('Content-Type:application/xml');
        echo $this->arrayToXml($return);        
    }

    private function arrayToXml($data){
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

    private function array_to_str_special($data){
        ksort($data);

        foreach ($data as $key => $value) {
            $valueArr[] = "$key=$value";
        }
        $keyStr = implode("&",$valueArr);

        $keyStr .= "&key=".$this->key;

        return strtoupper(md5($keyStr));
    }

}