<?php 
class Log_expense_model extends CI_Model {

    public function __construct()
    {
        parent::__construct('log_expense','id');
        $this->load->database();
    }


    
}
?>