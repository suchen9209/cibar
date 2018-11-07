<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>商品类型</title>
</head>
<body>
    <a href="<?=ADMIN_PATH?>/good_type/insert">增加新类型</a>
    <h1>类型(正在使用</h1>
    <table>
    	<tr>
    		<th>ID</th>
    		<th>name</th>
    		<th>status</th>
            <th>操作</th>
    	</tr>
    	<?php foreach($data as $k => $v):?>
    	<tr>
    		<td><?=$v['id']?></td>
    		<td><?=$v['name']?></td>
    		<td><?=$v['status']?></td>
            <td><a href="<?=ADMIN_PATH?>/good_type/update/<?=$v['id']?>">修改</a></td>
    	</tr>
    	<?php endforeach;?>  	
    </table>

    <h1>类型(停用</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>name</th>
            <th>status</th>
            <th>操作</th>
        </tr>
        <?php foreach($data_un as $k => $v):?>
        <tr>
            <td><?=$v['id']?></td>
            <td><?=$v['name']?></td>
            <td><?=$v['status']?></td>
            <td><a href="<?=ADMIN_PATH?>/good_type/update/<?=$v['id']?>">修改</a></td>
        </tr>
        <?php endforeach;?>     
    </table>

</body>
</html>
