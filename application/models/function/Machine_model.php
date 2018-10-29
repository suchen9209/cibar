<?php 
class Machine_model extends CI_Model {

    /*
        绑定用户和账户
    */

    public function __construct()
    {
        $this->load->database();
    }

    public function get_machine_number($type,$number){
        return 5;
    }

}
?>