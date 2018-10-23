<?php 
class Adminuser_model extends CI_Model {

	private $table_name = 'adminuser';

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
        $this->db->where('id', $id);
        $this->db->update($this->table_name, $parm);
        return $this->db->affected_rows();
    }

    public function get_user_info($id=0){
    	if($id!=0){
    		$this->db->select('*');
    		$this->db->where('id', $id);
    		$this->db->from($this->table_name);
    		$query = $this->db->get();
    		return $query->row();
    	}else{
    		return false;
    	}
    }

    public function get_admin_list($page,$num,$option=array()){
        $offset = ($page-1)*$num;
        $this->db->select('*');
        $this->db->limit($num,$offset);
        foreach ($option as $key => $value) {
            $this->db->where($key,$value);
        }
        $query = $this->db->get($this->table_name);
        return $query->num_rows() > 0 ? $query->result_array() : false;

    }

    
}
?>