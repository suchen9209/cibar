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

