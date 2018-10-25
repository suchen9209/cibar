<?php 
class Appointment_model extends CI_Model {

	private $table_name = 'appointment';

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

    public function get_info($uid=0){
    	if($uid!=0){
    		$this->db->select('*');
    		$this->db->where('uid', $uid);
    		$this->db->from($this->table_name);
    		$query = $this->db->get();
    		return $query->result_array();
    	}else{
    		return false;
    	}
    }

    /*
    *
    *
    */
    public function get_appointnum_in_date_and_number($time,$type,$number){

        $this->db->where('starttime <=',$time);
        $this->db->where('endtime >=',$time);
        $this->db->where('type',$type);
        $this->db->where('state',$this->config->item('appointment_status')['indate']);
        $this->db->from($this->table_name);

        if($type == $this->config->item('seat_type')['seat']){//散座

            $this->db->select_sum('number','number');
            $query = $this->db->get();
            return $query->row()->number;

        }else if($type == $this->config->item('seat_type')['box']){//包厢  

            $this->db->select('count(id) number');
            $this->db->where('number',$number);
            $query = $this->db->get();
            return $query->row()->number;

        }else{
            return false;
        }

    }

    
}
?>