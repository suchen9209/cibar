<?php 
class User_coupon_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('user_coupon','id');
    }

    public function get_list($offset,$num,$parm=array()){
        $this->db->select('*');
        $this->db->limit($num,$offset);
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_can_use_by_uid_type($uid,$type){
        $this->db->select('coupon.*,user_coupon.id as user_coupon_id');

        $this->db->join('coupon','coupon.id = user_coupon.cid');
        $this->db->where('user_coupon.uid',$uid);
        $this->db->where('coupon.type',$type);

        $this->db->where('starttime <',time());
        $this->db->where('endtime >',strtotime(date('Y-m-d 23:59:59')));
        $this->db->where('state',1);

        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }
    
}
?>