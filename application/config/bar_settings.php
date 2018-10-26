<?php 
//test git
/*
 *	充值方式
 */
$config["log_pay_type"] = array(
	"wx"		=>	1,
	"bar"		=>	2,
	"alipay"	=>	3
);


//机器的分布类型
$config['machine_type'] = array(
	'sanzuo'	=>	1,//散座
	'4ren'		=>	2,
);

//预约状态
$config['appointment_status'] = array(
	'init'		=>	0,
	'indate'	=>	1,
	'cancel'	=>	2,
	'complete'	=>	3
);

$config['seat_type'] = array(
	'init'	=>	0,
	'seat'	=> 	1,
	'box'	=>	2
);

//区分会员等级的金额,详见user_account_model的get_member_level
$config['member_level'] = array(499,999,4999,9999,19999,49999,99999);
//区分
$config['discount_level'] = array(
	0 => 1,
	1 => 0.98,
	2 => 0.95,
	3 => 0.90,
	4 => 0.85,
	5 => 0.80,
	6 => 0.75,
	7 => 0.70
);
