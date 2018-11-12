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

    public function get_num($option=array()){
        $this->db->select('count(*) num');
        foreach ($option as $key => $value) {
            $this->db->where($key,$value);
        }
        $query = $this->db->get($this->table_name);
        return $query->row()->num;
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

    public function get_machine_number($type,$number){
        if($type == 1){
            $this->db->select('count(*) as num');
            $this->db->where('machine.status',1);
            $this->db->where('machine.type',1);
            $query = $this->db->get($this->table_name);
            return $query->row()->num;
        }else{
            $number = intval($number);
            $sql = "SELECT COUNT(DISTINCT `box_id`) as num FROM `machine` WHERE ";
            switch ($number) {
                case 5:
                    $sql.= "`type` = 2 or `type` = 3 ";
                    break;
                case 6:
                    $sql.= "`type` = 4 ";
                    break;
                case 10:
                    $sql.= "`type` = 5 ";
                    break;
                case 20:
                    $sql.= "`type` = 6 ";
                    break;
            }

            $query =$this->db->query($sql);
            return $query->row()->num;
        }
    }

    
}
?>