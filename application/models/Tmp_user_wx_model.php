<?php 
class Tmp_user_wx_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('tmp_user_wx','id');
    }

    public function get_tmp_id_by_unionid($unionid=0){
    	if($unionid!=0){
    		$this->db->select('*');
    		$this->db->where('unionid', $unionid);
    		$this->db->from($this->table_name);
    		$query = $this->db->get();
    		return $query->row();
    	}else{
    		return false;
    	}
    }

}
?>