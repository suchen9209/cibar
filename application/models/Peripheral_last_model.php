<?php 
class Peripheral_last_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('peripheral_last','id');
    }

    public function get_last_by_uid($uid){
    	$this->db->select('*');
        $this->db->where('uid',$uid);
        $query = $this->db->get($this->table_name);
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }
    
}
?>