<?php 
class Order_status_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('order_status','id');
        $this->load->database();
    }

    


    
}
?>