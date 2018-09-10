<?php
namespace controllers;
use libs\Redis;
class TestController
{
    
    public function regist()
    {
        // 注册成功

        // 发邮件
        $redis = \libs\Redis::getInstance();

        $data = [
            'email' => 'fortheday@126.com',
            'title' => '标题',
            'content' => '内容',
        ];
        $data = json_encode($data);
        $redis->lpush('email',$data);
        echo "注册成功";
    }

    public function mail()
    {
        header('Content-Type:text/plain;charset=utf-8');
        // 发邮件
        
        // 设置socket永不超时
        ini_set('default_socket_timeout', -1);

        // echo iconv("GB2312//IGNORE","UTF-8", "邮件队列已启动...等待中...");
        echo "the email already to send ";

        $redis = \libs\Redis::getInstance();

        while(true)
        {
            // 默认列表取数据设置永不超时
            $data = $redis->brpop('email', 0);
            echo '开始发邮件';
            // 处理数据
            var_dump($data);

            echo "发完邮件，继续等待\r\n";
        }

    }

    public function testMail1()
    {
        $mail = new \libs\Mail;
        $mail->send('测试', '测试8756', ['2747561946@qq.com', '爱人']);
    }

        public function testMail()
        {
            $transport = (new \Swift_SmtpTransport('smtp.126.com', 25))
            ->setUsername('dweeq369@126.com')
            ->setPassword('dweeq369');

            //创建发邮件对象
            $mailer = new \Swift_Mailer($transport);
            //创建消息信息
            $message = new \Swift_Message();
            $message->setSubject('测试标题')  //标题
                    ->setFrom(['dweeq369@126.com' => 'XXF'])  //发件人
                    ->setTo(['2747561946@qq.com','2747561946@qq.com' => 'aaa']) //收件人
                    ->setBody('Hello <a href="http://localhost:9999">点击激活</a> Word ', 'text/html'); //邮件内容及邮件类型
            
                    //发送
            $set = $mailer->send($message);
            var_dump($set);
        }
}