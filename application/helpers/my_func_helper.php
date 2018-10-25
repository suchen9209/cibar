<?php
/**
 * 常用函数
 */
function checkUserLogin()
{
    $isLogin = false;
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id']))
    {
        $isLogin = false;
    }
    else
    {
        $isLogin = true;
    }
    return $isLogin;
}

function checkAdminLogin()
{
	$isLogin = false;
	if (!isset($_SESSION['uid']) || empty($_SESSION['uid']))
    {
        $isLogin = false;
    }
    else
    {
        $isLogin = true;
    }
    return $isLogin;
}

function checkAdminRole(array $role)
{
    $hasRole = false;
    if (!checkAdminLogin()) 
    {
        $hasRole = false;
    }else if ($_SESSION['role']) {
        if (in_array($_SESSION['role'], $role)) {
            $hasRole = true;
        } else {
            $hasRole = false;
        }
    }

    return $hasRole;
}

function makeRandomSessionName($len){
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


function is_timestamp($timestamp) {
    if(strtotime(date('Y-m-d H:i:s',$timestamp)) === $timestamp) {
        return $timestamp;
    } else return false;
}

