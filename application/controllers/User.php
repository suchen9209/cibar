<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('user_model','user');
	}

	public function index()
	{
		var_dump($_SESSION);
		var_dump(checkUserLogin());
		$user_info = $this->user->get_user_info(1);

		$this->load->view('welcome_message');
	}

	public function forcelogin(){
		
		$this->session->user_id = 5;
        $this->session->uid = 3;
		var_dump($this->session);
    }

    public function clean_memcache(){
        $this->load->driver('cache');
        $this->cache->memcached->save('service', json_encode([]), 60*60*48);
    }

    public function forcelogout(){
    	session_destroy();
    }

    public function makesessionname($len){

        $fp = @fopen('/dev/urandom', 'rb');

        $result = '';

        if ($fp !== FALSE) {

            $result .= @fread($fp, $len);

            @fclose($fp);

        } else {

            trigger_error('Can not open /dev/urandom.'); 

        }

        // convert from binary to string

        $result = base64_encode($result);

        // remove none url chars

        $result = strtr($result, '+/', '-_');
        


        return substr($result, 0, $len);


    }
}
