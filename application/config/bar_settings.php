<?php 
//test git
/*
 *	充值方式
 */
$config["log_pay_type"] = array(
	"wx"		=>	1,
	"scan"		=>	2,
	"cash"		=>	3,
	"pos"		=>	4,
	"gift"		=>	5,
	"meituan"	=>	6
);

$config['log_pay_type_cn'] = array(
	1	=>	'微信',
	2	=>	'扫码',
	3	=>	'现金',
	4	=>	'POS',
	5	=>	'赠送',
	6	=>	'美团'
);

//acitve_status表中status字段含义
$config['active_status'] = array(
	0	=>	'初始入库',
	1	=>	'空闲',
	2	=>	'正在使用',
	3	=>	'预约'
);

//log_login中login_logout的含义
$config['log_login'] = array(
	'login'		=>	1,
	'logout'	=>	2
);
//log_login中type的含义
$config['log_login_type'] = array(
	'bar'		=>	1//暂时只有吧台上下机一种
);

$config['order_status_status'] = array(
	'init'	=>	0,
	'done'	=>	1
);

//peripheral中type的含义
$config['peripheral_type'] = array(
	1	=>	'鼠标',
	2	=>	'键盘',
	3	=>	'耳机',
	4	=>	'鼠标垫',
	5	=>	'麦克风',
	6	=>	'其他'
);


//机器的分布类型
$config['machine_type'] = array(
	1	=>	'散座',//散座
	2	=>	'5人包厢（环型）',
	3	=>	'5人包厢（线型）',
	4	=>	'6人包厢',
	5	=>	'10人包厢',
	6	=>	'20人包厢'
);
//对应机器的价格
$config['price'] = array(
	1	=>	'30',//散座
	2	=>	'20',
	3	=>	'20',
	4	=>	'15',
	5	=>	'15',
	6	=>	'15'
);

//机器硬件状态，是否能够使用
$config['machine_hardware_status'] = array(
	1	=>	'正常',
	2	=>	'损坏'
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

$config['status_common'] = array(
	1	=>	'正在使用',
	0	=>	'停用'
);


