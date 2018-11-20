<?php 
class Log_login_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('log_login','id');
    }

    public function get_last_login_info($uid){
    	$this->db->select('*');
        $this->db->where('uid',$uid);
        $this->db->where('login_or_logout',1);
        $this->db->order_by('id','DESC');
        $this->db->limit(1,0);
        $query = $this->db->get($this->table_name);

        return $query->row();
    }
    
}
?>