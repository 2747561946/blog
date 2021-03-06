<?php
namespace controllers;

use PDO;

class MockController
{
    public function users()
    {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','123');
        $pdo->exec('set names utf8');

        $pdo->exec('TRUNCATE users');

        for($i=0;$i<20;$i++)
        {
            $email = rand(100000,9999999999).'@126.com';
            $password = md5('123123') ;
           
            // $user_id = rand(1,20);
            // $pdo->exec("insert into blogs(title,content) value('$title','$content')");
            $pdo->exec("INSERT INTO users (email,password) VALUES('$email','$password')");
        }  
    }

    public function blog()
    {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','123');
        $pdo->exec('set names utf8');

        $pdo->exec('TRUNCATE blogs');

        for($i=0;$i<300;$i++)
        {
            $title = $this -> getChar( rand(10,60) );
            $content = $this -> getChar( rand(50,300) );
            $display = rand(10,500);
            $is_show = rand(0,1);
            $date = rand(1233333399,1535592288);
            $date = date('Y-m-d H:i:s', $date);
            $user_id = rand(1,20);
            // $pdo->exec("insert into blogs(title,content) value('$title','$content')");
            $pdo->exec("INSERT INTO blogs (title,content,display,is_show,created_at,user_id) VALUES('$title','$content',$display,$is_show,'$date',$user_id)");
        }  


    }


    function getChar($num)  // $num为生成汉字的数量
    {
        $b = '';
        for ($i=0; $i<$num; $i++) {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    }

    function cs (){

        $mail = new \libs\Mail;
        $mail->send();
    }
 

}