<?php 
class Log_login_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('log_login','id');
    }

    public function get_last_login_info($uid){
    	$this->db->select('*');
        $this->db->where('uid',$uid);
        $this->db->order_by('id','DESC');
        $this->db->limit(1,0);
        $query = $this->db->get($this->table_name);

        return $query->row();
    }

    public function get_total_time($stime,$etime){
        //3部分
        //完全在这个时间段内的
        $this->db->select('SUM(`logout_time` - `time`) time1');
        $this->db->where('time >',$stime);
        $this->db->where('logout_time <',$etime);
        $this->db->from($this->table_name);
        $query = $this->db->get();
        $time1 = $query->row()->time1 ? $query->row()->time1 : 0;

        //在这个时间段内结束的
        $this->db->select('SUM(`logout_time` - '.$stime.') time2');
        $this->db->where('time <',$stime);
        $this->db->where('logout_time <',$etime);
        $this->db->where('logout_time >',$stime);
        $this->db->from($this->table_name);
        $query = $this->db->get();
        $time2 = $query->row()->time2 ? $query->row()->time2 : 0;

        //在这个时间段开始的
        $this->db->select('SUM('.$etime.' - `time`) time3');
        $this->db->where('time >',$stime);
        $this->db->where('time <',$etime);
        $this->db->where('logout_time >',$etime);
        $this->db->from($this->table_name);
        $query = $this->db->get();
        $time3 = $query->row()->time3 ? $query->row()->time3 : 0;

        return $time1 + $time2 + $time3;

    }

    public function get_people_num($stime,$etime){
        $this->db->select('COUNT(*) num');
        $this->db->where('logout_time >',$stime);
        $this->db->where('time <',$etime);
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row()->num ? $query->row()->num : 0;
   
    }
    
}
?>