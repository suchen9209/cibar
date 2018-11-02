<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>后台登录</title>
</head>
<body>
    <h1>后台登录</h1>
    <form action='<?=ADMIN_PATH?>/login_page?action=login' method="post">
        <input type="text" name="username" />
        <input type="password" name="password" />
        <input type="submit" name="submit"/>
    </form>

</body>
</html>
