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

        if($list){
            $return_data = array();
            array_multisort(array_column($list, 'time'),$list);

            $return_data['start_time'] = $list[0]['time'];
            $return_data['end_time'] = end($list)['time'];
            $return_data['total_time'] = end($list)['time'] - $list[0]['time'];
            $return_data['whopay'] = $list[0]['pay_uid'];
            $return_data['type'] = $list[0]['type'];
            $return_data['money'] = 0;
            $return_data['total_money'] = 0;
            $return_data['overnignt'] = false;
            foreach ($list as $key => $value) {
                $return_data['total_money'] += floatval($value['discount_money']);
                $return_data['money'] += floatval($value['money']);
                if($value['money'] == 0){
                    $return_data['overnignt'] = true;
                }
            }
            $return_data['total_money'] = round($return_data['total_money'],2);
            $return_data['money'] = round($return_data['money'],2);

            return $return_data;
        }else{
            return false;
        }

        
    }

    public function delete_by_uid($uid){
        $this->db->where('uid', $uid);
        $this->db->delete($this->table_name);
        return $this->db->affected_rows();
    }
    
}
?>