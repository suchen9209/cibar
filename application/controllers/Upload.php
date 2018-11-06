<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function index()
    {
        header('content-type:text/html charset:utf-8');
        $date = date('ym');
        if(HTTP_HOST == 'www.esports222.com'){
            $dir_base = "./uploads/".$date."/";     //文件上传根目录
            is_dir($dir_base) OR mkdir($dir_base, 0777, true);
            $show_dir_base = "/uploads/".$date."/";  
        }else if(HTTP_HOST == 'bar.suchot.com'){
            $dir_base = "/data/uploads/images/".$date."/";
            is_dir($dir_base) OR mkdir($dir_base, 0777, true);
            $dir_base = "/".$date."/";
        }else{
            $dir_base = '';
            is_dir($dir_base) OR mkdir($dir_base, 0777, true);
            $dir_base = "/".$date."/";
        }
        
        //没有成功上传文件，报错并退出。
        $output = "<textarea>";
        $index = 0;        //$_FILES 以文件name为数组下标，不适用foreach($_FILES as $index=>$file)
        foreach($_FILES as $file){
            $upload_file_name = 'upload_file' . $index;        //对应index.html FomData中的文件命名
            $filename = $_FILES[$upload_file_name]['name'];
            $gb_filename = iconv('utf-8','gb2312',$filename);    //名字转换成gb2312处理
            //文件不存在才上传
            if(!file_exists($dir_base.$gb_filename)) {
                $isMoved = false;  //默认上传失败
                $MAXIMUM_FILESIZE = 5 * 1024 * 1024;     //文件大小限制    1M = 1 * 1024 * 1024 B;
                $rEFileTypes = "/^\.(jpg|jpeg|gif|png){1}$/i"; 
                if ($_FILES[$upload_file_name]['size'] <= $MAXIMUM_FILESIZE && preg_match($rEFileTypes, strrchr($gb_filename, '.'))) {
                    $isMoved = @move_uploaded_file($_FILES[$upload_file_name]['tmp_name'], $dir_base.$gb_filename);        //上传文件
                }
            }else{
                $isMoved = true;    //已存在文件设置为上传成功
            }
            if($isMoved){
                //输出图片文件<img>标签
                //注：在一些系统src可能需要urlencode处理，发现图片无法显示，
                //请尝试 urlencode($gb_filename) 或 urlencode($filename)，不行请查看HTML中显示的src并酌情解决。
                $output .= "<img src='{$show_dir_base}{$filename}' title='{$filename}' alt='{$filename}'/>";
            }else {
                //上传失败则把error.jpg传回给前端
                $output .= "<img src='{$show_dir_base}error.jpg' title='{$filename}' alt='{$filename}'/>";
            }
            $index++;
        }
        echo $output."</textarea>";
    }

}
