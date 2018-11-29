# /api/appointment
查看所有预约

# /api/appointment/num
查看预约数量，按照座位类型分类

# /api/machine
查看所有空闲机器，可通过type获取对应类型的空闲机器
## 1. type
  ### description
    机器类型
  ### value
  * 1: 散座',//散座
  * 2: 5人包厢（环型
  * 3: 5人包厢（线型
  * 4: 6人包厢
  * 5: 10人包厢
  * 6: 20人包厢

# /api/machine/all
查看所有机器

# /api/machine/order
上机
## 1. user_id
      用户ID，查询用户时返回
## 2. machine_id
      机器ID,api/machine接口中返回
      
      
# /api/machine/down
下机，及下机时显示的用户状态
## 1. user_id
## 2. op
  ### value
  * get: 获取当前用户使用的机器信息
  * down: 下机
      
# /api/user
获取用户信息(参数提交一个即可
## 1. user_id
## 2. phone
  
# /api/user/pay
充值
## 1. user_id
## 2. number
## 3. type
  ### value
  * 1: 微信
  * 2: 扫码
  * 3: 现金
  * 4: POS
  * 5: 赠送
  * 6: 美团途径
## 4. extra_number
  ### description
    店长赠送，默认为0
    
# /api/user/get_live_user
获取正在上机的用户
## 1. page
  页码

# /api/user/get_user_num
获取总用户数

# /api/user/get_user_list
获取用户列表
## 1. page
## 2. limit
## 3. order_option(选
  ### value
  * balance
  * total
  * lasttime
  * regtime
## 4. order(选
  ### value
  * ASC
  * DESC
## 5.offline(选
  * 1(有此参数则取离线用户)
  
# /api/user/bind_info/
绑定身份证
## 1. user_id
## 2. idcard
## 3. name
## 4. phone

# /api/user/get_log_pay
获取充值记录
## 1. page
## 2. num
## 3. uid(选添)

# /api/user/get_log_expense
获取消费记录
## 1. page
## 2. num
## 3. uid(选添)

# /api/user/get_active_user_info
获取在线用户信息（包含上机时间
## 1. user_id

# /api/goods
获取商品列表和商品类型
## 1. page
## 2. num

# /api/goods/get_on_list
获取手机上下单的未完成状态订单
## 1. page
## 2. limit

# /api/goods/done_order
完成订单
## 1. order_id

# /api/goods/calculate_discount
计算订单折扣
## 1.user_id
## 2.cartList

# /api/goods/buy
前台订单确认购买（确认并返回成功视为订单完成）
## 1.user_id
## 2.cartList
## 3.total
## 4.payment


# /api/config
获取支付方式的类别id


# /api/peripheral
获取空闲外设列表
## 1.user_id
        用于显示该用户上次使用的外设

# /api/peripheral/out
分配外设
## 1.user_id
## 2.pjson
      示例：[{"type":1,"id":1},{"type":2,"id":3},{"type":3,"id":7},{"type":4,"id":5}]
      其中type为外设类型，id为外设id，从/api/peripheral中获得

# /api/peripheral/get_list
获取所有外设列表
## 1. page
## 2. limit

# /api/checkin/single_info
上机流程前获取信息
## 1.user_id

# /api/checkin/single?san_or_box=san
散客上机
## 1. user_id
      用户ID，查询用户时返回
## 2. machine_id
## 3. pjson
      示例：[{"type":1,"id":1},{"type":2,"id":3},{"type":3,"id":7},{"type":4,"id":5}]
      其中type为外设类型，id为外设id    


# /api/checkin/single?san_or_box=box
整包上机
## 1. user_id
      用户ID，查询用户时返回
## 2. box_id
## 3. pay_type
## 4. whopay
      要求对应用户的ID
## 5. pjson
      示例：[{"type":1,"id":1},{"type":2,"id":3},{"type":3,"id":7},{"type":4,"id":5}]
      其中type为外设类型，id为外设id   


# /api/service
获取当前所有的呼叫服务的mid

# /api/service/cancel
取消服务
## 1. user_id