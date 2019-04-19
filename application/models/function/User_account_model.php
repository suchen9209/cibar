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
        $this->load->model('tmp_user_wx_model','tmp_user_wx');
        $this->load->model('active_status_model','active_status');
        $this->load->model('vip_level_special_model','vip_level_special');
    }

    private function save_info($parm){
        $this->load->driver('cache');
        foreach ($parm as $key => $value) {
            $this->cache->memcached->save($key, $value, 60*60*24*30);
        }
    }

    public function register($type,$parm){
        $time = time();
        if($type == 'wx'){
/*            $insert_parm = array();
            $insert_parm['regtime'] = $time;
            $insert_parm['lasttime'] = $time;
            $insert_parm['wxid'] = $parm['openid'];
            $insert_parm['wxunionid'] = $parm['unionid'];
            $insert_parm['wxsessionkey'] = $parm['wxsessionkey'];
            $user_id = $this->user->insert($insert_parm);
            $username = member_id($user_id,$time);
            $this->user->update($user_id,array('username'=>$username));

            $account_pram['uid'] =$user_id;
            $account_pram['regtime'] = $time;
            $account_pram['lasttime'] = $time;
            $this->account->insert($account_pram);*/

            $tmp_user = $this->tmp_user_wx->get_tmp_id_by_unionid($parm['unionid']);
            $tmp_id = $tmp_user->id;
            if($tmp_id){
                $this->tmp_user_wx->update($tmp_id,array('sessionkey'=>$parm['wxsessionkey']));
            }else{
                $insert_parm = array();
                $insert_parm['openid'] = $parm['openid'];
                $insert_parm['unionid'] = $parm['unionid'];
                $insert_parm['sessionkey'] = $parm['wxsessionkey'];
                $insert_parm['regtime'] = $time;
                $tmp_id = $this->tmp_user_wx->insert($insert_parm);   
            }           

            $session_name = makeRandomSessionName(16);
            $this->save_info(array($session_name=>'tmp'.$tmp_id));
        }

        return $session_name;
    }

    public function login($type,$parm){
        if($type == 'wx'){
            $user = $this->user->get_info_u('wxid',$parm['openid']);
            $this->user->update($user->id,array('lasttime'=>time(),'wxsessionkey'=>$parm['wxsessionkey']));
            $uid = $user->id;
            
            $session_name = makeRandomSessionName(16);
            $this->save_info(array($session_name=>$uid));
            return $session_name;
        }else if($type == 'direct_uid'){
            $uid = $parm['uid'];
            
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

    public function get_member_level($uid,$total=-1){
        $vip_level_info = $this->vip_level_special->get_info($uid);
        if($vip_level_info && $vip_level_info->endtime > time()){
            return $vip_level_info->level;
        }else{
            $member_level = $this->config->item('member_level');
            if($total == -1){//用于在已知总额的情况下，减少一次数据库查询
                $total = $this->account->get_info($uid)->total; 
            }        
            $level = $this->sorts($member_level,$total);

            return $level;    
        }

        
    }

    public function sorts($stage_data,$stage_num){
        array_push($stage_data, $stage_num);
        sort($stage_data);
        return array_search($stage_num, $stage_data);
    }

    public function get_user_num($parm=NULL){
        $this->db->select('count(*) num');
        $this->db->from('user');
        $this->db->join('account','user.id = account.uid');
        $this->db->join('active_status','user.id = active_status.uid','LEFT');
        if(isset($parm)){
            foreach ($parm as $key => $value) {
                $this->db->where($key,$value);
            }  
        }        
        $query = $this->db->get();
        return $query->row()->num;
    }

    public function get_user_list($num=20,$offset=0,$order_option,$order,$parm){

        $this->db->select('user.*,account.balance,account.total,active_status.state,vip_level_special.level as svip_level');
        $this->db->from('user');
        $this->db->join('account','user.id = account.uid','LEFT');
        $this->db->join('active_status','user.id = active_status.uid','LEFT');
        $this->db->join('vip_level_special','user.id = vip_level_special.uid','LEFT');
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->limit($num,$offset);
        $this->db->order_by($order_option,$order);
        $query = $this->db->get();

        if($query->num_rows()>0){
            $return_arr = $query->result_array();
            foreach ($return_arr as $key => $value) {
                $return_arr[$key]['level'] = $this->get_member_level($value['id'],$value['total']);
            }
            return $return_arr;
        }else{
            return false;
        }
    }
    
}
?>