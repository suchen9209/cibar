<?php 
class Goods_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('goods','id');
    }


    public function get_list($status = 1){
        $this->db->select('*');
        $this->db->where('status',$status);
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->result_array();
    }

    
}
?>