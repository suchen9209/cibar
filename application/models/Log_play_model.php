<?php 
class Log_play_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('log_play','id');
        $this->load->database();
    }

    public function get_list($offset,$num,$parm=array()){
        $this->db->select('log_play.*,user.name name,user.username vip_num,user.phone,user.idcard');
        $this->db->limit($num,$offset);

        $this->db->join('user','user.id = log_play.uid');
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_num($parm=array()){
        $this->db->select('count(*) num');
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row()->num;
    }

    public function get_detail($id){
        $this->db->select('log_play.*,user.name name,user.username vip_num,user.phone,user.idcard');
        $this->db->where('log_play.id',$id);
        $this->db->join('user','user.id = log_play.uid');
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row();

    }


    
}
?>