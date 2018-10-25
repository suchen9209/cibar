<?php 
class User_account_model extends CI_Model {

    /*
        绑定用户和账户
    */

    public function __construct()
    {
        $this->load->database();
        $this->load->model('user_model','user');
        $this->load->model('account_model','account');
        $this->load->model('log_pay_model','log_pay');
    }

    private function save_info($parm){
        $this->load->driver('cache');
        foreach ($parm as $key => $value) {
            $this->cache->memcached->save($key, $value, 60*60*48);
        }
    }

    public function register($type,$parm){
        if($type == 'wx'){
            $insert_parm = array();
            $insert_parm['regtime'] = time();
            $insert_parm['lasttime'] = time();
            $insert_parm['wxid'] = $parm['openid'];
            $insert_parm['wxunionid'] = $parm['unionid'];
            $user_id = $this->user->insert($insert_parm);

            $account_pram['uid'] =$user_id;
            $account_pram['regtime'] = time();
            $account_pram['lasttime'] = time();
            $this->account->insert($account_pram);

            $session_name = makeRandomSessionName(16);
            $this->save_info(array($session_name=>$user_id));
        }

        return $session_name;
    }

    public function login($type,$parm){
        if($type == 'wx'){
            $user = $this->user->get_info('wxid',$parm['openid']);
            $this->user->update($user->id,array('lasttime'=>time()));
            $uid = $user->id;
            
            $session_name = makeRandomSessionName(16);
            $this->save_info(array($session_name=>$uid));
            return $session_name;
        }

    }

    public function get_user_info($uid){
        $user_info = $this->user->get_user_info($uid);
        $account_info = $this->account->get_info($uid);
        $return_arr['uid'] = $user_info->id;
        $return_arr['name'] = $user_info->name;
        $return_arr['wxid'] = $user_info->wxid;
        $return_arr['balance'] = $account_info->balance;

        //会员系统保留字段
        $return_arr['level'] = '10';

        return $return_arr;
    }

    public function add_balance($uid,$parm){
        if($uid != 0 && $parm){
            $this->db->trans_start();

            $this->log_pay->insert($parm);
            $this->account->recharge($uid,$parm['money']);
             
            return $this->db->trans_complete();
        }else{
            return false;
        }
    }
    
}
?>