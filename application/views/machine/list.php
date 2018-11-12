<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>机器列表</title>
</head>
<body>
    <a href="<?=ADMIN_PATH?>/machine/insert">增加新机器</a>
    <h1>机器列表</h1>
    <table>
    	<tr>
    		<th>ID</th>
    		<th>机器编号</th>
    		<th>IP</th>
    		<th>房间类型</th>
    		<th>状态</th>
            <th>位置</th>
            <th>包厢标识</th>
            <th>操作</th>
    	</tr>
    	<?php foreach($data as $k => $v):?>
    	<tr>
    		<td><?=$v['id']?></td>
    		<td><?=$v['machine_name']?></td>
    		<td><?=$v['ip']?></td>
    		<td><?=$this->config->item('machine_type')[$v['type']]?></td>
    		<td><?=$this->config->item('machine_hardware_status')[$v['status']]?></td>
            <td><?=$v['position']?></td>
            <td><?=$v['box_id']?></td>
            <td><a href="<?=ADMIN_PATH?>/machine/update/<?=$v['id']?>">修改</a></td>
    	</tr>
    	<?php endforeach;?>

    	
    </table>

    <?php for($i=1;$i<=$page_count;$i++):?>
        <a href="<?=ADMIN_PATH?>/machine?page=<?=$i?>"><?=$i?></a>
    <?php endfor;?>

</body>
</html>
