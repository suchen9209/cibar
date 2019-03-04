<?php 
class Log_peripheral_in_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('log_peripheral_in','id');
    }

    public function get_list($offset,$num,$parm=array()){
    	$this->db->select('log_peripheral_in.*,peripheral_num.desc');
        $this->db->limit($num,$offset);
        foreach ($parm as $key => $value) {
        	$this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $this->db->join('peripheral_num','log_peripheral_in.pnid = peripheral_num.id','LEFT');
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }
    

    public function get_num($parm=array()){
        $this->db->select('count(*) num');
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row()->num;
    }

}
?>