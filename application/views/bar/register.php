<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>注册新的工作人员</title>
</head>
<body>
    <h1>注册新的工作人员</h1>
    <form action='<?=ADMIN_PATH?>/admin/register?action=register' method="post">
        <label>用户名</label><input type="text" name="username" />
        <label>密码</label><input type="password" name="password" />
        <label>手机</label><input type="text" name="phone" />
        <label>姓名</label><input type="text" name="name" />
        <label>角色</label><input type="text" name="role" />
        <input type="submit" name="submit"/>
    </form>

</body>
</html>
