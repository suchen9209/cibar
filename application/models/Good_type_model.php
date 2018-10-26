<?php 
class Good_type_model extends CI_Model {

	private $table_name = 'good_type';

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

    public function get_list($status = 1){
        $this->db->select('*');
        $this->db->where('status',$status);
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->result_array();
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