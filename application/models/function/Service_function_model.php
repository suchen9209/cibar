<?php 
class Service_function_model extends CI_Model {

    /*
        绑定用户和账户
    */
    private $service_memcached_name = 'service';

    public function __construct()
    {
        $this->load->database();
        $this->load->model('function/user_seat_model','user_seat');
        $this->load->driver('cache');
    }

    public function call_service($uid){
        $seat_info = $this->user_seat->get_seat($uid);
        $service_json = $this->cache->memcached->get($this->service_memcached_name);
        if($service_json){
            $service = json_decode($service_json,true);
            $key = array_search($seat_info, $service);
            if ($key !== false){//二次呼叫移至头部
                array_splice($service, $key, 1);
                array_unshift($service, $seat_info);
            }else{
                $service[]= $seat_info; 
            }
            
        }else{
            $service = array($seat_info);
        }
        
        return $this->cache->memcached->save($this->service_memcached_name,json_encode($service),3600*24);
    }

    public function remove_service($uid){
        $seat_info = $this->user_seat->get_seat($uid);
        $service_json = $this->cache->memcached->get($this->service_memcached_name);
        $service = json_decode($service_json,true);

        $key = array_search($seat_info, $service);
        if ($key !== false){
            array_splice($service, $key, 1);
        }
        return $this->cache->memcached->save($this->service_memcached_name,json_encode($service),3600*24);
    }


    public function get_all_service(){
        $service_json = $this->cache->memcached->get($this->service_memcached_name);

        return $service_json;
    }


}
?>