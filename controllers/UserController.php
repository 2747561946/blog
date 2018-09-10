<?php
namespace controllers;
use libs\Redis;
//引入
use models\User;
class UserController
{
    public function register()
    {
        view('users.add');
    }

    public function hello()
    {
        $user = new User;
        $name = $user->getName();

        view('users.hello', [
            'name' => $name
        ]);
    }

    public function word()
    {
        echo 'word';
    }

    public function store()
    {
        // 接受表单
        $email = $_POST['email'];
        $password= md5($_POST['password']);

        //插入数据库
        $user = new User;
        $ret = $user->add($email,$password);

        if(!$ret)
        {
           die('注册失败');
        }
        
        //消息队列

         //从邮箱地址取出姓名
        $name = explode('@',$email);
        $from = [$email, $name[0]];

        //构造消息数组
        $message = [
            'title' => '你是猪吗',
            'content' => "点击一下连接进行激活:<br><a href=''>点击激活</a>",
            'from' => $from,
        ];

        //发邮件
        // $mail = new \libs\Mail;
        // $content = "注册成功啦";
       

        //把消息转成字符串
        $message = json_encode($message);

        //放到队列
        $redis = \libs\Redis::getInstance();
      
        // $message = explode()
        $redis->lpush('email', $message);
        // $mail->send('注册成功', $content, $from);
        
        echo "ok.";
    }
}