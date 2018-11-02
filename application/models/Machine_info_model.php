<?php 
class Machine_info_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('machine_info','machine_id');
    }    
}
?>