<?php 
class Log_pay_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('log_pay','id');
    }

    public function get_list($offset,$num,$parm=array()){
    	$this->db->select('*');
        $this->db->limit($num,$offset);
        foreach ($parm as $key => $value) {
        	$this->db->where($key,$value);
        }
        $this->db->order_by('time','DESC');
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_tool_list($offset,$num,$parm=array()){
        $this->db->select('log_pay.*,user.name name,user.username vip_num,user.phone,user.idcard');
        $this->db->join('user','user.id = log_pay.uid');
        $this->db->limit($num,$offset);
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

    public function get_total_money($parm=array()){
        $this->db->select('SUM(money) money,SUM(extra_num) extra_num');
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row()->money + $query->row()->extra_num;
    }
}
?>