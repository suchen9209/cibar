<?php 
class Shift_log_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('shift_log','id');
        $this->load->database();
    }

    public function get_last_time($ouid = 0){
        $this->db->select('time');
/*        if($ouid != 0){
            $this->db->where('ouid',$ouid);
        }*/
        $this->db->order_by('time','DESC');
        $this->db->limit(1,0);
        $query = $this->db->get($this->table_name);
        return $query->row()->time;
    }


    
}
?>