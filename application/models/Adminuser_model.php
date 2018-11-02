<?php 
class Adminuser_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('adminuser','id');
    }

    public function get_info_by_username($username){
        $this->db->select('*');
        $this->db->where('username',$username);
        $query = $this->db->get($this->table_name);

        return $query->row();
    }

    public function get_admin_list($page,$num,$option=array()){
        $offset = ($page-1)*$num;
        $this->db->select('*');
        $this->db->limit($num,$offset);
        foreach ($option as $key => $value) {
            $this->db->where($key,$value);
        }
        $query = $this->db->get($this->table_name);
        return $query->num_rows() > 0 ? $query->result_array() : false;

    }

    
}
?>