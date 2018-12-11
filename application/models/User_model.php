<?php 
class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('user','id');
    }

    public function get_user_info($id=0){
    	if($id!=0){
    		$this->db->select('*');
    		$this->db->where($this->primary_key, $id);
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

    public function get_info_u($key,$value){
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

    public function get_new_member($stime,$etime){
        $this->db->select('COUNT(*) num');
        $this->db->where('regtime >',$stime);
        $this->db->where('regtime <',$etime);
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row()->num ? $query->row()->num : 0;
   
    }
    
}
?>