<?php 
class User_coupon_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('user_coupon','id');
    }

    public function get_list($offset,$num,$parm=array()){
        $this->db->select('coupon.*,user_coupon.id as user_coupon_id');
        $this->db->join('coupon','coupon.id = user_coupon.cid');
        $this->db->limit($num,$offset);
        foreach ($parm as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_can_use_by_uid_type($uid,$type){
        $this->db->select('coupon.*,user_coupon.id as user_coupon_id,user_coupon.endtime as endtime');

        $this->db->join('coupon','coupon.id = user_coupon.cid');
        $this->db->where('user_coupon.uid',$uid);
        if($type != 0){
            $this->db->where('coupon.type',$type);    
        }        

        $this->db->where('user_coupon.starttime <',time());
        $this->db->where('user_coupon.endtime >',strtotime(date('Y-m-d 23:59:59')));
        $this->db->where('user_coupon.state',1);

        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : array();
    }

    public function get_cannot_use_by_uid_type($uid,$type){
        $this->db->select('coupon.*,user_coupon.id as user_coupon_id,user_coupon.endtime as endtime');
        $this->db->join('coupon','coupon.id = user_coupon.cid','left');
        $this->db->where('user_coupon.uid',$uid);
        if($type != 0){
            $this->db->where('coupon.type',$type);    
        }        

        $this->db->where('user_coupon.endtime <',strtotime(date('Y-m-d 23:59:59',strtotime('-1 day'))));
        $this->db->where('user_coupon.state',1);

        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : array();
    }

    public function get_used_coupon_by_uid_type($uid,$type){
        $this->db->select('coupon.*,user_coupon.id as user_coupon_id,user_coupon.endtime as endtime');
        $this->db->join('coupon','coupon.id = user_coupon.cid','left');
        $this->db->where('user_coupon.uid',$uid);
        if($type != 0){
            $this->db->where('coupon.type',$type);    
        }        

        $this->db->where('user_coupon.state',2);

        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : array();
    }

    public function use_coupon($id,$log_play_id=0,$order_status_id=0){
        $parm = array('state' => 2, 'usetime' => time(),'log_play_id'=>$log_play_id,'order_status_id'=>$order_status_id);
        $this->db->where($this->primary_key, $id);
        $this->db->update($this->table_name, $parm);
        return $this->db->affected_rows();
    }
    
}
?>