<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * The base controller which is used by the Front and the Admin controllers
 */
class MY_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();	
	}//end __construct()

	
}//end MY_Controller

class Admin_Controller extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();  
        if(!checkAdminLogin()){
            redirect(ADMIN_PATH.'/login_page');
        }
    }//end __construct()

    
}//end MY_Controller

require_once __MYDIR__.'/application/libraries/REST_Controller.php';

class App_Api_Controller extends REST_Controller
{
    protected $messageMap = [
        parent::HTTP_OK => '数据获取成功', //200
        parent::HTTP_CREATED => '添加成功', //201
        parent::HTTP_BAD_REQUEST => '缺少参数或参数错误', //400
        parent::HTTP_NOT_FOUND => '暂无数据', //404
        parent::HTTP_UNPROCESSABLE_ENTITY=>'添加失败', //422
        parent::HTTP_CONFLICT=>'已存在' //409
    ];

    function __construct($config='app_rest',$session_name=''){
        parent::__construct($config,$session_name);
        date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * @param $code 状态码
     * @param string $message 提示信息 支持自定义，null表示使用messageMap
     * @param array $data 响应数据
     * @return array response信息
     */
    public function getResponseData($code , $message = '' , $data = null)
    {
        return [
            'code' => $code,
            'message' => $message ? $message : $this->messageMap[$code],
            'data' => $data
        ];
    }

    public function getUserId(){
        $this->load->driver('cache');
        $uid = $this->cache->memcached->get($this->get_session_name());
        if(strpos($uid,'tmp') !== false){
            $this->response($this->getResponseData(parent::HTTP_BAD_REQUEST, '尚未绑定手机号', '尚未绑定手机号'), parent::HTTP_OK);
        }else{
            return $uid;    
        }
        
    }


}


require_once __MYDIR__.'/application/libraries/REST2_Controller.php';

class Test_Api_Controller extends REST2_Controller
{
    protected $messageMap = [
        parent::HTTP_OK => '数据获取成功', //200
        parent::HTTP_CREATED => '添加成功', //201
        parent::HTTP_BAD_REQUEST => '缺少参数或参数错误', //400
        parent::HTTP_NOT_FOUND => '暂无数据', //404
        parent::HTTP_UNPROCESSABLE_ENTITY=>'添加失败', //422
        parent::HTTP_CONFLICT=>'已存在' //409
    ];

    function __construct($config='test_rest'){
        parent::__construct('test_rest');
        date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * @param $code 状态码
     * @param string $message 提示信息 支持自定义，null表示使用messageMap
     * @param array $data 响应数据
     * @return array response信息
     */
    public function getResponseData($code , $message = '' , $data = null)
    {
        return [
            'code' => $code,
            'message' => $message ? $message : $this->messageMap[$code],
            'data' => $data
        ];
    }

    public function getUserId(){
        return $this->session->user_id;
    }


}


class Admin_Api_Controller extends REST2_Controller
{
    protected $messageMap = [
        parent::HTTP_OK => '数据获取成功', //200
        parent::HTTP_CREATED => '添加成功', //201
        parent::HTTP_BAD_REQUEST => '缺少参数或参数错误', //400
        parent::HTTP_NOT_FOUND => '暂无数据', //404
        parent::HTTP_UNPROCESSABLE_ENTITY=>'添加失败', //422
        parent::HTTP_CONFLICT=>'已存在' //409
    ];

    function __construct($config='admin_rest'){
        header('Access-Control-Allow-Origin:*');
        parent::__construct($config);
        date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * @param $code 状态码
     * @param string $message 提示信息 支持自定义，null表示使用messageMap
     * @param array $data 响应数据
     * @return array response信息
     */
    public function getResponseData($code , $message = '' , $data = null)
    {
        return [
            'code' => $code,
            'message' => $message ? $message : $this->messageMap[$code],
            'data' => $data
        ];
    }

    public function getLayuiList($code , $message = '' , $count = 0,  $data = null)
    {
        return [
            'code' => $code,
            'msg' => $message ? $message : $this->messageMap[$code],
            'count' => $count,
            'data' => $data
        ];
    }



}


