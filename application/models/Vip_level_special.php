<?php 
class Vip_level_special_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('vip_level_special','uid');
    }

    public function get_list($offset,$num,$option=array()){
        $this->db->select('*');
        $this->db->limit($num,$offset);
        foreach ($option as $key => $value) {
            $this->db->where($key,$value);
        }
        $query = $this->db->get($this->table_name);
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_num($option=array()){
        $this->db->select('count(*) num');
        foreach ($option as $key => $value) {
            $this->db->where($key,$value);
        }
        $query = $this->db->get($this->table_name);
        return $query->row()->num;
    }
    
}
?>