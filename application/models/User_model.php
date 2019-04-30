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
        }elseif($type == 'phone'){
            $this->db->select('id');
            $this->db->where('phone',$parm['phone']);  
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row();
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

    public function get_user_info_by_phone(){

    }

    public function get_user_info_by_parm($parm){
        $this->db->select('*')->from($this->table_name);

        if(strlen($parm) == 18){
            $this->db->where('idcard', $parm);
        }else if(is_numeric($parm) && strlen($parm) == 11){
            $this->db->where('phone', $parm);
        }else if(is_numeric($parm)){
            $this->db->where('id', $parm);
        }else{
            $this->db->where('username', $parm);   
        }               
        
        $query = $this->db->get();
        return $query->row();
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