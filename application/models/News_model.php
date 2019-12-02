<?php 
class News_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('news','id');
    }

    public function get_list($offset,$num,$parm = array()){
        $this->db->select('*');
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value); 
        }
        
        $this->db->limit($num,$offset);
        $this->db->order_by('id','DESC');
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_num($parm = array()){
        $this->db->select('count(*) num');
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row()->num;
    }

    public function delete($id){
        $this->db->where('id', $id);
        $this->db->delete($this->table_name);
        return $this->db->affected_rows();
    }

    
}
?>