<?php 
class Account_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('account','uid');
    }

    //充值
    public function recharge($uid,$num){
        $this->db->where('uid',$uid);
        $this->db->set('balance', 'balance +'.$num, false);
        $this->db->set('total', 'total +'.$num, false);
        $this->db->update($this->table_name);
        return $this->db->affected_rows();
    }

    //消费
    public function expense($uid,$num){
        $this->db->where('uid',$uid);
        $this->db->set('balance', 'balance -'.$num, false);
        $this->db->update($this->table_name);
        return $this->db->affected_rows();
    }

    
}
?>