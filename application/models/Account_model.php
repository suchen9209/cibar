<?php 
class Account_model extends CI_Model {

	private $table_name = 'account';

    public function __construct()
    {
        $this->load->database();
    }

    public function insert($parm)
    {
        $this->db->insert($this->table_name, $parm);
        return $this->db->insert_id();
    }

    public function update($uid, $parm)
    {
        $this->db->where('uid', $uid);
        $this->db->update($this->table_name, $parm);
        return $this->db->affected_rows();
    }

    public function recharge($uid,$num){
        $this->db->where('uid',$uid);
        $this->db->set('balance', 'balance +'.$num, false);
        $this->db->set('total', 'total +'.$num, false);
        $this->db->update($this->table_name);
        return $this->db->affected_rows();

    }

    public function get_info($uid=0){
    	if($uid!=0){
    		$this->db->select('*');
    		$this->db->where('uid', $uid);
    		$this->db->from($this->table_name);
    		$query = $this->db->get();
    		return $query->row();
    	}else{
    		return false;
    	}
    }

    
}
?>