<?php 
class Log_expense_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('log_expense','id');
        $this->load->database();
    }

    public function get_list($offset,$num,$parm=array()){
    	$this->db->select('log_expense.*,goods.name good');
        $this->db->limit($num,$offset);
        $this->db->join('goods','goods.id = log_expense.goodid');
        foreach ($parm as $key => $value) {
        	$this->db->where($key,$value);
        }
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->result_array() : false;
    }

    public function get_tool_list($offset,$num,$parm=array()){
        $this->db->select('log_expense.*,user.name name,user.username vip_num,user.phone,user.idcard');
        $this->db->limit($num,$offset);

        if($parm['log_expense.type'] != 0){
            $this->db->select('goods.name good_name');
            $this->db->join('goods','goods.id = log_expense.goodid');
        }        
        $this->db->join('user','user.id = log_expense.uid');
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
        $this->db->select('log_expense.*,goods.name good_name,goods.img good_img,goods.price good_price');
        $this->db->where('log_expense.id',$id);
        $this->db->join('goods','goods.id = log_expense.goodid');
        $this->db->from($this->table_name);
        $query = $this->db->get();
        return $query->row();

    }


    
}
?>