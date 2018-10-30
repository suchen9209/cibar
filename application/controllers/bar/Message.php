<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message extends CI_Controller {

	public function index(){        
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');

        $this->load->driver('cache');
        $service_json = $this->cache->memcached->get('service');

        $time = date('r');
        echo "data: {$service_json}";
        flush();

	}

    public function html(){
        $this->load->view('bar/test');
    }

}
