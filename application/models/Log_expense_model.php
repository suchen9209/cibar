<?php 
class Log_expense_model extends CI_Model {

	private $table_name = 'log_expense';

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
        $this->db->where('id', $uid);
        $this->db->update($this->table_name, $parm);
        return $this->db->affected_rows();
    }

    public function get_info($id=0){
    	if($id!=0){
    		$this->db->select('*');
    		$this->db->where('id', $uid);
    		$this->db->from($this->table_name);
    		$query = $this->db->get();
    		return $query->row();
    	}else{
    		return false;
    	}
    }

    
}
?>