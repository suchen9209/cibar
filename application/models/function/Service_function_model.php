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
        $this->load->model('active_status_model','active_status');
        $this->load->driver('cache');
    }

    public function call_service($uid){
        $user_info = $this->active_status->get_info_uid($uid);
        $seat_info = $user_info->mid;
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

        $send_parm = array();
        $send_parm['uid'] = $uid;
        $send_parm['mid'] = $seat_info;
        $send_parm['cmd'] = 'call_service';
        $this->send_wokerman->send(json_encode($send_parm));
        
        return $this->cache->memcached->save($this->service_memcached_name,json_encode($service),3600*24);
    }

    public function remove_service($uid){
        $user_info = $this->active_status->get_info_uid($uid);
        $seat_info = $user_info->mid;
        $service_json = $this->cache->memcached->get($this->service_memcached_name);
        $service = json_decode($service_json,true);

        $key = array_search($seat_info, $service);
        if ($key !== false){
            array_splice($service, $key, 1);
        }
        if($this->cache->memcached->save($this->service_memcached_name,json_encode($service),3600*24)){
            return $seat_info;
        }else{
            return false;
        }
    }


    public function get_all_service(){
        $service_json = $this->cache->memcached->get($this->service_memcached_name);

        return $service_json;
    }

    public function get_active_service(){
        
    }


}
?>