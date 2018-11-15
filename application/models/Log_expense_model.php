<?php 
class Log_expense_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('log_expense','id');
        $this->load->database();
    }

    public function get_list($offset,$num,$parm=array()){
    	$this->db->select('log_expense.*,goods.name good');
        $this->db->limit($num,$offset);
        $this->db->join('goods','goods.id = log_expense.goodid');
        foreach ($parm as $key => $value) {
        	$this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
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