<?php
namespace libs;

class Mail
{   
    public $mailer;
    public function __construct()
    {
        $config = config('email');
        //设置邮件服务器账号
        $transport = (new \Swift_SmtpTransport($config['host'], $config['port']))
        ->setUsername($config['name'])
        ->setPassword($config['pass']);

        //创建发邮件对象
        
        $this->mailer = new \Swift_Mailer($transport);
        // var_dump($this->mailer);
    }

    public function send($title, $content, $to)
    {
        //创建消息信息
        $message = new \Swift_Message();
        $message->setSubject($title)  //标题
                ->setFrom([$config['from_email'] => $config['from_name']])  //发件人
                ->setTo([
                    $to[0],
                    $to[0] => $to[1]
                ]) //收件人
                ->setBody($content, 'text/html'); //邮件内容及邮件类型
        
                //发送
                
       $this->mailer->send($message);
       
    }
}