# /api/appointment
查看所有预约

# /api/machine
查看所有空闲机器，可通过type获取对应类型的空闲机器
parm:
  1.type
    description:
      机器类型
    value:
      1	=>	'散座',//散座
      2	=>	'5人包厢（环型）',
      3	=>	'5人包厢（线型）',
      4	=>	'6人包厢',
      5	=>	'10人包厢',
      6	=>	'20人包厢'

# /api/machine/order
上机
parm:
  1.user_id
    description:
      用户ID，查询用户时返回
  2.machine_id
      机器ID,api/machine接口中返回
      
      
# /api/machine/down
下机，及下机时显示的用户状态
parm:
  1.user_id
  2.op
    value:
      get:获取当前用户使用的机器信息
      down:下机
      
# /api/user
获取用户信息
parm:(两个参数二选一）
  1.user_id
  2.phone
  
# /api/user/pay
充值
parm:
  1.user_id
  2.number
  3.type
    "微信"		=>	1,
    "扫码"		=>	2,
    "现金"		=>	3,
    "POS"		=>	4,
    "赠送"		=>	5,
    "美团途径"	=>	6
  4.extra_number
    description:店长赠送，无则添0
    
# /api/user/get_live_user
获取正在上机的用户
parm:
  1.page

# /api/user/get_detail_info
获取用户信息
# /api/user/get_user_num
获取总用户数


# /api/user/get_user_list
获取用户列表
parm:
  1.page
  2.order_option(选
    value:
      balance
      total
      lasttime
      regtime
  3.order(选
    value:
      ASC
      DESC
  
