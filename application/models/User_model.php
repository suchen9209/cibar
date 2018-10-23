<?php 
class User_model extends CI_Model {

	private $table_name = 'user';

    public function __construct()
    {
        $this->load->database();
    }

    public function insert($parm)
    {
        $this->db->insert($this->table_name, $parm);
        return $this->db->insert_id();
    }

    public function update($id, $parm)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table_name, $parm);
        return $this->db->affected_rows();
    }

    public function get_user_info($id=0){
    	if($id!=0){
    		$this->db->select('username,phone,nickname,name,regtime,lasttime');
    		$this->db->where('id', $id);
    		$this->db->from($this->table_name);
    		$query = $this->db->get();
    		return $query->row();
    	}else{
    		return false;
    	}
    }

    public function check_user($type,$parm){
        if($type == 'wx'){
            $this->db->select('id');
            $this->db->where('wxid',$parm['openid']);
            $this->db->from($this->table_name);
            $query = $this->db->get();
            return $query->row();
        }
    }

    public function get_info($key,$value){
        if($key && $value){
            $this->db->select('*');
            $this->db->where($key , $value);
            $this->db->from($this->table_name);
            $query = $this->db->get();
            return $query->row();
        }else{
            return false;
        }
    }

    
}
?>