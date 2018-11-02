<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>修改信息</title>
</head>
<body>
    <h1>修改</h1>
    <form action='<?=ADMIN_PATH?>/machine/update/<?=$data->id?>?action=update' method="post">
        <label>机器编号</label><input type="text" name="machine_name" value="<?=$data->machine_name?>" />
        <label>IP</label><input type="text" name="ip" value="<?=$data->ip?>" />
        <label>房间类型</label>
        <select name="type">
            <?php foreach ($this->config->item('machine_type') as $key => $value) : ?>
            <option value="<?=$key?>" <?php if($data->type == $key) echo 'selected'; ?> ><?=$value?></option>    
            <?php endforeach;?>
        </select>
        <select name="status">
            <?php foreach ($this->config->item('machine_hardware_status') as $key => $value) : ?>
            <option value="<?=$key?>" <?php if($data->status == $key) echo 'selected'; ?> ><?=$value?></option>    
            <?php endforeach;?>
        </select>
        <label>位置</label><input type="text" name="position" value="<?=$data->position?>" />
        <label>包厢标识</label><input type="text" name="box_id" value="<?=$data->box_id?>" />
        <input type="submit" name="submit"/>
    </form>

</body>
</html>
