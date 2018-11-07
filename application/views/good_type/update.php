<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>修改类型</title>
</head>
<body>
    <h1>修改类型</h1>
    <form action='<?=ADMIN_PATH?>/good_type/update/<?=$data->id?>?action=update' method="post">
        <label>类型名</label><input type="text" name="name" value="<?=$data->name?>" />
        <label>类型状态</label>
        <select name="status">
            <?php foreach ($this->config->item('status_common') as $key => $value) : ?>
            <option value="<?=$key?>" <?php if($data->status == $key) echo 'selected'; ?> ><?=$value?></option>    
            <?php endforeach;?>
        </select>
        <input type="submit" name="submit"/>
    </form>

</body>
</html>
