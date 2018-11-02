<?php 
class Machine_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('machine','id');
    }

    public function get_machine_list($page,$num,$option=array()){
        $offset = ($page-1)*$num;
        $this->db->select('*');
        $this->db->limit($num,$offset);
        foreach ($option as $key => $value) {
            $this->db->where($key,$value);
        }
        $query = $this->db->get($this->table_name);
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_active_machine($type=0){
        $this->db->select('*');
        $this->db->join('active_status','active_status.mid = machine.id','left');
        $this->db->where('active_status.state',1);
        $this->db->where('machine.status',1);
        if($type > 0){
            $this->db->where('machine.type',$type);
        }
        $query = $this->db->get($this->table_name);

        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    
}
?>