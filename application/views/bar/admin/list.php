<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>管理人员列表</title>
</head>
<body>
    <h1>管理人员列表</h1>
    <table>
    	<tr>
    		<th>ID</th>
    		<th>用户名</th>
    		<th>姓名</th>
    		<th>手机号码</th>
    		<th>角色</th>
    	</tr>
    	<?php foreach($admin_data as $k => $v):?>
    	<tr>
    		<td><?=$v['id']?></td>
    		<td><?=$v['username']?></td>
    		<td><?=$v['name']?></td>
    		<td><?=$v['phone']?></td>
    		<td><?=$v['role']?></td>
    	</tr>
    	<?php endforeach;?>

    	
    </table>

</body>
</html>
