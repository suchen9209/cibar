<?php 
class Good_type_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('good_type','id');
    }

    public function get_list($status = 1){
        $this->db->select('*');
        if($status != -1){
            $this->db->where('status',$status);   
        }        
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->result_array();
    }

    
}
?>