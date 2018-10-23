<?php 
class User_account_model extends CI_Model {

    /*
        绑定用户和账户
    */

    public function __construct()
    {
        $this->load->database();
        $this->load->model('user_model','user');
    }

    public function register($type,$parm){
        if($type == 'wx'){
            $insert_parm = array();
            $insert_parm['regtime'] = time();
            $insert_parm['lasttime'] = time();
            $insert_parm['wxid'] = $parm['openid'];
            $insert_parm['wxunionid'] = $parm['unionid'];
            $user_id = $this->user->insert($insert_parm);


        }

        return $user_id;

    }

    public function login($type,$parm){
        if($type == 'wx'){
            $user = $this->user->get_info('wxid',$parm['openid']);

            $this->user->update($user->id,array('lasttime'=>time()));
            return $user->id;
        }



    }


    
}
?>