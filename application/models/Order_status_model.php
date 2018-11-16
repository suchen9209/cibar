<?php 
class Order_status_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('order_status','id');
        $this->load->database();
    }

    public function get_list($offset,$num,$parm=array()){
    	$this->db->select('order_status.*,user.name,machine.machine_name,machine.box_id');
        $this->db->limit($num,$offset);
        foreach ($parm as $key => $value) {
        	$this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $this->db->join('user','user.id = order_status.uid','LEFT');
        $this->db->join('active_status','active_status.uid = order_status.uid','LEFT');
        $this->db->join('machine','active_status.mid = machine.id','LEFT');
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