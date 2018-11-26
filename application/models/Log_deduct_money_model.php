<?php 
class Log_deduct_money_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('log_deduct_money','id');
    }

/*    public function get_last_login_info($uid){
    	$this->db->select('*');
        $this->db->where('uid',$uid);
        $this->db->where('login_or_logout',1);
        $this->db->order_by('id','DESC');
        $this->db->limit(1,0);
        $query = $this->db->get($this->table_name);

        return $query->row();
    }*/

    public function get_total_info($uid){
        $this->db->select('*');
        $this->db->where('uid',$uid);
        $query = $this->db->get($this->table_name);

        $list = $query->result_array();

        $return_data = array();
        array_multisort(array_column($list, 'time'),$list);

        $return_data['start_time'] = $list[0]['time'];
        $return_data['end_time'] = end($list)['time'];
        $return_data['whopay'] = $list[0]['pay_uid'];
        $return_data['total_money'] = 0;
        foreach ($list as $key => $value) {
            $return_data['total_money'] += $value['discount_money'];
        }

        return $return_data;
    }

    public function delete_by_uid($uid){
        $this->db->where('uid', $uid);
        $this->db->delete($this->table_name);
        return $this->db->affected_rows();
    }
    
}
?>