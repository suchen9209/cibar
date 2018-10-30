<?php 
class User_seat_model extends CI_Model {

    /*
        绑定用户和账户
    */

    public function __construct()
    {
        $this->load->database();
    }

    //通过用户id获取座位号
    public function get_seat($id){
        return rand();
    }

}
?>