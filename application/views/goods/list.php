<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>商品</title>
    
</head>
<body>

    <a href="<?=ADMIN_PATH?>/goods/insert">增加新商品</a>
    <h1>类型</h1>
    <table>
    	<tr>
    		<th>ID</th>
    		<th>name</th>
            <th>价格</th>
    		<th>status</th>
            <th>种类</th>
            <th>操作</th>
    	</tr>
    	<?php foreach($data as $k => $v):?>
    	<tr>
    		<td><?=$v['id']?></td>
    		<td><?=$v['name']?></td>
            <td><?=$v['price']?></td>
    		<td><?=$v['status']?></td>
            <td><?=$type[$v['type']]?></td>
            <td><a href="<?=ADMIN_PATH?>/goods/update/<?=$v['id']?>">修改</a></td>
    	</tr>
    	<?php endforeach;?>
    </table>

    <?php for($i=1;$i<=$page_count;$i++):?>
        <a href="<?=ADMIN_PATH?>/goods?page=<?=$i?>"><?=$i?></a>
    <?php endfor;?>

</body>
</html>
