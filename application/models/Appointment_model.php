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
        $today_0 = strtotime(date('Y-m-d 00:00:00',time()));
        $this->db->select('*');
        $this->db->where('uid',$uid);
        //$this->db->where('state',$this->config->item('appointment_status')['indate']);
        $this->db->where('starttime >',$today_0);
        $this->db->from($this->table_name);

        $query = $this->db->get();

        return $query->num_rows() > 0 ? $query->result_array() : false;
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

    public function get_appoint_today(){
        $this->db->select('appointment.*,user.name,user.phone,user.username');
        $today = date('Y-m-d 00:00:00');
        $this->db->where('starttime >',strtotime($today));
        $this->db->where('starttime <',strtotime($today)+3600*24);

        $this->db->where('state',$this->config->item('appointment_status')['indate']);
        $this->db->from($this->table_name);
        $this->db->join('user','appointment.uid = user.id','LEFT');

        $query = $this->db->get();

        return $query->num_rows() > 0 ? $query->result_array() : false;
    }


    public function get_appoint_after($offset,$num,$parm=array()){
        $this->db->select('appointment.*,user.name,user.phone,user.nickname');
        $today = date('Y-m-d 00:00:00');
        $this->db->where('starttime >',strtotime($today));

        //$this->db->where('state',$this->config->item('appointment_status')['indate']);
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $this->db->join('user','appointment.uid = user.id','LEFT');
        $this->db->order_by('starttime','ASC');
        $this->db->limit($num,$offset);

        $query = $this->db->get();

        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_appoint_after_num($parm=array()){
        $this->db->select('count(*) num');
        $today = date('Y-m-d 00:00:00');
        $this->db->where('starttime >',strtotime($today));

        //$this->db->where('state',$this->config->item('appointment_status')['indate']);
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $this->db->join('user','appointment.uid = user.id','LEFT');

        $query = $this->db->get();

        return $query->row()->num;
    }

}
?>
