<?php
namespace controllers;

class UploadController 
{
    public function upload()
    {
        //接收图片
        $file = $_FILES['image'];
        // 生成随机名
        $name = time();

        //移动图片
        move_uploaded_file($file['tmp_name'], ROOT . 'public/uploads/'.$name.'.png');

        //吧数组转成json并返回
        echo json_encode([
            'success' => true,
            // 'msg' => '打开方式不对',
            'file_path' => '/public/uploads/'.$name.'.png',
        ]);
    }
}