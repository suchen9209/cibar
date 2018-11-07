<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>增加新商品</title>
    <script type="text/javascript" src="<?php echo STYLE_PATH; ?>/js/jquery-3.3.1.js"></script>
    <style type="text/css">
        
    </style>
</head>
<body>
    <h1>增加新商品</h1>
    <form action='<?=ADMIN_PATH?>/goods/insert?action=insert' method="post">
        <label>商品名</label><input type="text" name="name" />
        <label>商品图片</label><input type="text" name="img" /><input type="file" id="inputfile" name="" class="photo">
        <label>商品类型</label>
        <select name="type">
            <?php foreach ($type as $key => $value) : ?>
            <option value="<?=$key?>"><?=$value?></option>    
            <?php endforeach;?>
        </select>
        <label>价格</label><input type="text" name="price" />
        <label>状态</label>
        <select name="status">
            <?php foreach ($this->config->item('status_common') as $key => $value) : ?>
            <option value="<?=$key?>"><?=$value?></option>    
            <?php endforeach;?>
        </select>
        <input type="submit" name="submit"/>
    </form>
<script type="text/javascript">
$(document).ready(function(){
    //响应文件添加成功事件
    $("#inputfile").change(function(){

        var file = $('#inputfile')[0].files[0];
        //创建FormData对象
        var data = new FormData();
        data.append('upload_file', file);
        $.ajax({
            url:'/upload', /*去过那个php文件*/
            type:'POST',  /*提交方式*/
            data:data,
            cache: false,
            contentType: false,        /*不可缺*/
            processData: false,         /*不可缺*/
            success:function(data){    
                if(data != 'error'){
                    $("input[name=img]").val(data);
                }else{
                    alert('上传出错');
                }
            },
            error:function(){
                alert('上传出错');
            }
        });
    });
});
</script>
</body>
</html>
