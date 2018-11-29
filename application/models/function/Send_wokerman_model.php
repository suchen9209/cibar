
<?php 
class Send_wokerman_model extends CI_Model {


    public function send($msg){
        $client = stream_socket_client('tcp://39.107.64.199:2347', $errno, $errmsg, 1);
    	fwrite($client, $msg."\n");
    }

}
?>