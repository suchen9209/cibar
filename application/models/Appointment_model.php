<?php
class Appointment_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('appointment','id');
    }


    public function delete_by_id($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->table_name);
        return $this->db->affected_rows();
    }

    public function get_info($uid=0){
    	if($uid!=0){
    		$this->db->select('*');
    		$this->db->where('uid', $uid);
    		$this->db->from($this->table_name);
    		$query = $this->db->get();
    		return $query->num_rows() > 0 ? $query->result_array() : false;
    	}else{
    		return false;
    	}
    }

    public function get_appoint_indate($uid){
        $this->db->select('*');
        $this->db->where('uid',$uid);
        $this->db->where('state',$this->config->item('appointment_status')['indate']);
        $this->db->from($this->table_name);

        $query = $this->db->get();

        return return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_apoint_near_date($uid=0,$time){
        $this->db->select('id');
        $this->db->where('starttime <=',$time);
        $this->db->where('endtime >=',$time);
        $this->db->where('uid',$uid);
        $this->db->where('state',$this->config->item('appointment_status')['indate']);
        $this->db->from($this->table_name);

        $query = $this->db->get();

        return $query->row();
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
