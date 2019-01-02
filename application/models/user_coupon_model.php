<?php 
class User_coupon_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('user_coupon','id');
    }

    public function get_list($offset,$num,$parm=array()){
        $this->db->select('*');
        $this->db->limit($num,$offset);
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    
}
?>