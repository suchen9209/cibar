<?php 
class Active_status_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('active_status','mid');
    }    

    public function get_info_uid($uid){
        $this->db->select('*');
        $this->db->where('uid',$uid);
        $query = $this->db->get($this->table_name);

        return $query->row();
    }

    public function get_live_user($offset=0,$num=20){
        $this->db->select('*');
        $this->db->join('user','user.id = active_status.uid','left');
        $this->db->join('machine','machine.id = active_status.mid','left');
        $this->db->join('account','account.uid = active_status.uid','left');
        $this->db->where('active_status.state',2);
        $this->db->limit($num,$offset);
        $query = $this->db->get($this->table_name);

        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_live_user_num(){
        $this->db->select('count(*) num');
        $this->db->join('user','user.id = active_status.uid','left');
        $this->db->join('machine','machine.id = active_status.mid','left');
        $this->db->join('account','account.uid = active_status.uid','left');
        $this->db->where('active_status.state',2);
        $this->db->limit($num,$offset);
        $query = $this->db->get($this->table_name);

        return $query->row()->num;
    }


}
?>