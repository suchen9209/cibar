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
        $this->load->model('active_status_model','active_status');
    }

    private function save_info($parm){
        $this->load->driver('cache');
        foreach ($parm as $key => $value) {
            $this->cache->memcached->save($key, $value, 60*60*48);
        }
    }

    public function register($type,$parm){
        $time = time();
        if($type == 'wx'){
            $insert_parm = array();
            $insert_parm['regtime'] = $time;
            $insert_parm['lasttime'] = $time;
            $insert_parm['wxid'] = $parm['openid'];
            $insert_parm['wxunionid'] = $parm['unionid'];
            $user_id = $this->user->insert($insert_parm);
            $username = member_id($user_id,$time);
            $this->user->update($user_id,array('username'=>$username));

            $account_pram['uid'] =$user_id;
            $account_pram['regtime'] = $time;
            $account_pram['lasttime'] = $time;
            $this->account->insert($account_pram);

            $session_name = makeRandomSessionName(16);
            $this->save_info(array($session_name=>$user_id));
        }

        return $session_name;
    }

    public function login($type,$parm){
        if($type == 'wx'){
            $user = $this->user->get_info_u('wxid',$parm['openid']);
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
        $active_status = $this->active_status->get_info_uid($uid);
        $return_arr['uid'] = $user_info->id;
        $return_arr['name'] = $user_info->name;
        $return_arr['wxid'] = $user_info->wxid;
        $return_arr['phone'] = $user_info->phone;
        $return_arr['balance'] = $account_info->balance;
        $return_arr['regtime'] = $user_info->regtime;
        $return_arr['username'] = $user_info->username;
        if($active_status){
            $return_arr['active'] = true;
        }else{
            $return_arr['active'] = false;
        }

        //会员系统保留字段
        $return_arr['level'] = $this->get_member_level($uid);

        return $return_arr;
    }

    public function add_balance($uid,$parm){
        if($uid != 0 && $parm){
            $this->db->trans_start();

            $this->log_pay->insert($parm);
            $this->account->recharge($uid,$parm['money']);
            if(isset($parm['extra_num']) && $parm['extra_num']>0){
                $this->account->recharge($uid,$parm['extra_num']);
            }

            if($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                return false;
            }else{
                $this->db->trans_complete();
                return true;
            }   
        }else{
            return false;
        }
    }

    public function get_member_level($uid){
        $member_level = $this->config->item('member_level');
        $total = $this->account->get_info($uid)->total;
        $level = $this->sorts($member_level,$total);

        return $level;
    }

    public function sorts($stage_data,$stage_num){
        array_push($stage_data, $stage_num);
        sort($stage_data);
        return array_search($stage_num, $stage_data);
    }

    public function get_user_detail_info($uid=0,$offset=0,$num=20){
        $user_info = $this->user->get_user_info($uid);
        $account_info = $this->account->get_info($uid);
        if($uid == 0){
            $sql = '';
        }else{

        }
        $user_info = $this->user->get_user_info($uid);
        $account_info = $this->account->get_info($uid);
        $return_arr['uid'] = $user_info->id;
        $return_arr['name'] = $user_info->name;
        $return_arr['wxid'] = $user_info->wxid;
        $return_arr['phone'] = $user_info->phone;
        $return_arr['balance'] = $account_info->balance;
        $return_arr['regtime'] = $user_info->regtime;
        $return_arr['username'] = $user_info->username;

        //会员系统保留字段
        $return_arr['level'] = $this->get_member_level($uid);

        return $return_arr;
    }
    
}
?>