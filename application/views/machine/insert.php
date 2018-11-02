<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>增加新机器</title>
</head>
<body>
    <h1>增加新机器</h1>
    <form action='<?=ADMIN_PATH?>/machine/insert?action=insert' method="post">
        <label>机器编号</label><input type="text" name="machine_name" />
        <label>IP</label><input type="text" name="ip" />
        <label>房间类型</label>
        <select name="type">
            <?php foreach ($this->config->item('machine_type') as $key => $value) : ?>
            <option value="<?=$key?>"><?=$value?></option>    
            <?php endforeach;?>
        </select>
        <label>位置</label><input type="text" name="position" />
        <label>包厢标识</label><input type="text" name="box_id" />
        <input type="submit" name="submit"/>
    </form>

</body>
</html>
