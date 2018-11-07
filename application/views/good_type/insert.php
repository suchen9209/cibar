<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>增加新商品类型</title>
</head>
<body>
    <h1>增加新商品类型</h1>
    <form action='<?=ADMIN_PATH?>/good_type/insert?action=insert' method="post">
        <label>类型名称</label><input type="text" name="name" />
        <label>status</label>
        <select name="status">
            <?php foreach ($this->config->item('status_common') as $key => $value) : ?>
            <option value="<?=$key?>"><?=$value?></option>    
            <?php endforeach;?>
        </select>
        <input type="submit" name="submit"/>
    </form>

</body>
</html>
