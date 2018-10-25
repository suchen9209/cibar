<?php 
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
