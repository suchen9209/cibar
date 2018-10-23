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
    }

    private function set_session($parm){
        foreach ($parm as $key => $value) {
            $_SESSION[$key] = $value;
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

            $this->set_session(array('uid'=>$user_id));


        }

        return $user_id;

    }

    public function login($type,$parm){
        if($type == 'wx'){
            $user = $this->user->get_info('wxid',$parm['openid']);
            $this->user->update($user->id,array('lasttime'=>time()));
            $uid = $user->id;
            $this->set_session(array('uid'=>$uid));
            return $uid;
        }

    }



    
}
?>