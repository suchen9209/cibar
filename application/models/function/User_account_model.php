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

        }

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

    
}
?>