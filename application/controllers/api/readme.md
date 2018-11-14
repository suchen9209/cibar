# /api/appointment
查看所有预约

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
## 2. order_option(选
  ### value
  * balance
  * total
  * lasttime
  * regtime
## 3. order(选
  ### value
  * ASC
  * DESC
  
# /api/user/bind_info/idcard
绑定身份证
## 1. user_id
## 2. idcard
## 3. name

# /api/user/bind_info/phone
绑定手机号
## 1. user_id
## 2. phone


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

# /api/goods
获取商品列表和商品类型
## 1. page
## 2. num
