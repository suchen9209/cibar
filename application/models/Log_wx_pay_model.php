<?php 
class Log_wx_pay_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('log_wx_pay','id');
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

    public function get_info_by_out_trade_no($out_trade_no){
        $this->db->select('*');
        $this->db->where('out_trade_no',$out_trade_no);
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row();
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