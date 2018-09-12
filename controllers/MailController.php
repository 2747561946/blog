<?php
namespace controllers;

use libs\Mail;
use libs\Redis;

class MailController
{
    public function send()
    {
  
        $redis = \libs\Redis::getInstance();

        $mailer = new Mail;
        
        // 设置php永不超时
        ini_set('default_socket_timeout', -1);
        echo "The mail queue is activated successfully.....\r\n";
        while(true)
        {
            //先取消息
            // 0 没有消息堵塞 直到有消息才往后执行
            $data = $redis->brpop('email', 0);
           
            //发邮件
            // 取出消息并反序列化
            // json_decode:默认吧数据转成对象设置第二个参数(TRUE)才能转成数组
            $message = json_decode($data[1], TRUE);
           
            //发邮件
            $mailer->send($message['title'], $message['content'], $message['from']);
            
            echo "Send successfully wait to continue\r\n";
        }
        
    }
}