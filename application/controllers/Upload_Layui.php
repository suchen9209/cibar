<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload_Layui extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function index()
    {
        header('Access-Control-Allow-Origin:*');
        header('content-type:text/html charset:utf-8');
        $date = date('ym');
        if(HTTP_HOST == 'www.esports222.com'){
            $dir_base = "./uploads/".$date."/";     //文件上传根目录
            is_dir($dir_base) OR mkdir($dir_base, 0777, true);
            $show_dir_base = "/uploads/".$date."/";  
        }else if(HTTP_HOST == 'bar.suchot.com'){
            $dir_base = "/data/uploads/images/".$date."/";
            is_dir($dir_base) OR mkdir($dir_base, 0777, true);
            $show_dir_base = "/".$date."/";
        }else{
            $dir_base = __MYDIR__.'/uploads/'.$date."/";
            is_dir($dir_base) OR mkdir($dir_base, 0777, true);
            $show_dir_base = "/".$date."/";
        }

        $upload_file = $_FILES['file'];

        $extend = pathinfo($upload_file['name']);//获取文件后缀名
        
        $gb_filename = time().rand(1,100).'.'.$extend['extension'];    

        $isMoved = false;  //默认上传失败
        $MAXIMUM_FILESIZE = 5 * 1024 * 1024;     //文件大小限制    1M = 1 * 1024 * 1024 B;
        $allow_type = array('image/png','image/gif','image/jpg','image/jpeg','image/bmp');
        if ($upload_file['size'] <= $MAXIMUM_FILESIZE && in_array($upload_file['type'], $allow_type)) {
            $isMoved = @move_uploaded_file($upload_file['tmp_name'], $dir_base.$gb_filename);        //上传文件
        }

        header('Content-type:text/json');
        if($isMoved){
            $return_data['code'] = 0;
            $return_data['msg'] = '上传成功';
            $pic_url = 'http://'.HTTP_HOST.$show_dir_base.$gb_filename;
            $return_data['data'] = array('src'=>$pic_url);
            //$output = 'http://'.HTTP_HOST.$show_dir_base.$gb_filename;
        }else {
            $return_data['code'] = 400;
            $return_data['msg'] = '上传失败';
            $return_data['data'] = array('src'=>'000');
            //$output = "error";
        }
        echo json_encode($return_data);
    }

}
