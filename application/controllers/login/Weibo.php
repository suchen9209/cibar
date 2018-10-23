<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weibo extends ImbaTV_Controller {
    public function __construct(){
    	//include_once( 'config.php' );
    	require_once(dirname(__FILE__)."/Weibo/config.php");
    	require_once(dirname(__FILE__)."/Weibo/saetv2.ex.class.php");

        parent::__construct();
        $this->load->model('common/user_login_model', 'user_login');    
    }

    public function forcelogin(){
    	$_SESSION['imba_nickname']   =   'suchot';
        $_SESSION['imba_uid']        =   373300;
    }

    public function forcelogout(){
    	session_destroy();
    }

    public function index(){
		$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

		$code_url = $o->getAuthorizeURL( WB_CALLBACK_URL );
		//$data['code_url'] = $code_url;
		redirect($code_url);
        //$this->load->view('common/weibo',$data);        
    }

    public function callback(){
    	$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

		if (isset($_REQUEST['code'])) {
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = WB_CALLBACK_URL;
			try {
				$token = $o->getAccessToken( 'code', $keys ) ;
			} catch (OAuthException $e) {
			}
		}

		if ($token) {
			$_SESSION['token'] = $token;

			$user_info_url = 'https://api.weibo.com/2/users/show.json';
			$user_info_json = $o->oAuthRequest($user_info_url,'GET',array('access_token'=>$token['access_token'],'uid'=>$token['uid']));
			$user_info = json_decode($user_info_json,true);
			if($user_info['error']){
				$user_info['nickname'] = '微博用户'.$token['uid'];
				$user_info['avatar'] = '';
			}

			//var_dump($o->oGetUserInfo($token['access_token'], $token['uid']));die;

			setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );//weibo本地记录允许登录

			$user = $this->user_login->check_user_exist('wb',$token['uid']);
			if($user){//已注册
				$this->user_login->user_login_multi('wb',$user,$redirect_url);
			}else{//未注册
				$this->user_login->user_register_multi('wb',$token,$user_info);
			}
			
		} else {
			exit('<script language="javascript">alert("微博登录失败"); document.location.href="//www.imbatv.cn";</script>');
		}


    }





}