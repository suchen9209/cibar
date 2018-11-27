<?php 
class Peripheral_num_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('peripheral_num','id');
    }

    public function get_list_free($type = 0){
        $this->db->select('*');
        if($type != 0){
            $this->db->where('type',$type);
        }  
        $this->db->where('count >',0);
        $query = $this->db->get($this->table_name);
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_list($num=20,$offset=0){
        $this->db->select('*');
        $this->db->limit($num,$offset);
        $query = $this->db->get($this->table_name);
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_count(){
        $this->db->select('count(*) num');
        $query = $this->db->get($this->table_name);
        return $query->row()->num;
    }

    public function get_type_num(){
        $this->db->select('SUM(total) as total,type');
        $this->db->group_by('type');
        $query = $this->db->get($this->table_name);
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }
    public function out($id){
        $this->db->where('id',$id);
        $this->db->set('count', 'count - 1', false);
        $this->db->update($this->table_name);
        return $this->db->affected_rows();
    }

    public function in($id){
        $this->db->where('id',$id);
        $this->db->set('count', 'count + 1', false);
        $this->db->update($this->table_name);
        return $this->db->affected_rows();
    }
    
}
?>