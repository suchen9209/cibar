<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message extends CI_Controller {

	public function index(){        
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');

        $this->load->driver('cache');
        $service_json = $this->cache->memcached->get('service');
        echo "data: {$service_json}\n\n";
        flush();

        while (1) {
            $service_json_recent = $this->cache->memcached->get('service_recent');
            $service_json = $this->cache->memcached->get('service');

            if(($service_json_recent && $service_json_recent != $service_json)){//和之前的信息有区别时发送信息 或首次打开
                $this->cache->memcached->save('service_recent',$service_json);
                echo "data: {$service_json}\n\n";
                flush();
            }else{
                //do nothing
            }


            sleep(1);
        }

	}

    public function html(){
        $this->load->view('bar/test');
    }

    public function random_service(){
        $this->load->driver('cache');
        $this->cache->memcached->save('service',mt_rand());
    }

}
