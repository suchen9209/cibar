<?php 
//test git
/*
 *	充值方式
 */
$config["log_pay_type"] = array(
	"wx"		=>	1,
	"wx_f"		=>	2,
	"scan"		=>	3,
	"cash"		=>	4,
	"pos"		=>	5,
	"gift"		=>	6,
	"meituan"	=>	7
);

$config['log_pay_type_cn'] = array(
	1	=>	'小程序自充',
	2	=>	'前台微信支付',
	3	=>	'扫码',
	4	=>	'现金',
	5	=>	'POS',
	6	=>	'赠送',
	7	=>	'美团'
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

//log_deduct_money中type的含义
$config['log_deduct_money_type'] = array(
	'散客'		=>	1,
	'包厢AA'	=>	2,
	'包厢请客'	=>	3
);
//box_status中pay_type的含义
$config['box_status_pay_type'] = array(
	2	=>	'AA',
	3	=>	'请客'
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
//peripheral中state的含义
$config['peripheral_state'] = array(
	0	=>	'空闲',
	1	=>	'正在使用'
);
//peripheral中status的含义
$config['peripheral_status'] = array(
	1	=>	'正常',
	2	=>	'损坏'
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
	1	=>	'25',//散座
	2	=>	'30',
	3	=>	'30',
	4	=>	'30',
	5	=>	'30',
	6	=>	'30'
);

//对应包厢的价格
$config['box_price'] = array(
	1	=>	'25',//散座
	2	=>	'150',
	3	=>	'150',
	4	=>	'180',
	5	=>	'300',
	6	=>	'600'
);

//对应包厢的通宵价格
$config['box_overtime_price'] = array(
	2	=>	'500',
	3	=>	'500',
	4	=>	'600',
	5	=>	'1000',
	6	=>	'2000'
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


