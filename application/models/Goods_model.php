<?php 
class Goods_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('goods','id');
    }


    public function get_list($status = 1, $num = -1, $offset = -1, $parm = array()){
        $this->db->select('*');
        if($status != -1){
            $this->db->where('status',$status); 
        }

        foreach ($parm as $key => $value) {
            $this->db->where($key,$value); 
        }
        
        if($offset != -1 && $num != -1){
            $this->db->limit($num,$offset);
        }
        $this->db->order_by('id','DESC');
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_num($status = 1){
        $this->db->select('count(*) num');
        $this->db->where('status',$status);
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row();
    }


    
}
?>