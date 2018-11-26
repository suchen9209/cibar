<?php 
class Box_status_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('box_status','uid');
    }    

    public function get_info_uid($uid){
        $this->db->select('*');
        $this->db->where('uid',$uid);
        $query = $this->db->get($this->table_name);

        return $query->row();
    }

    public function delete_by_uid($uid){
        $this->db->where('uid', $uid);
        $this->db->delete($this->table_name);
        return $this->db->affected_rows();
    }

    public function get_num_by_box_id($box_id){
        $this->db->select('count(*) num');
        $this->db->where('box_id',$box_id);
        $query = $this->db->get($this->table_name);
        return $query->row()->num;
    }


}
?>