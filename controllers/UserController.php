<?php
namespace controllers;
use libs\Redis;
//引入
use models\User;
use Intervention\Image\ImageManagerStatic as Image;
class UserController
{

    // public function setavatar(){

    // }

    public function avatar()
    {
         view('sers.avatar');
    }
   public function setavatar()
   {
        //    上传新头像
        $upload = \libs\Uploader::make();
        $path = $upload->upload('avatar', 'avatar');
        // 裁切图片
        
        $image = Image::make( ROOT . 'public/uploads/' . $path );
        $image->crop((int)$_POST['w'], (int)$_POST['h'], (int)$_POST['x'], (int)$_POST['y']);
        // 保存时覆盖原图
        $image->save(ROOT . 'public/uploads/' .$path);
        // var_dump($image);
        // die;
        
        // 保存到表中
        $model = new \models\User;
        $model->setAvatar('/uploads/'.$path);

        // 删除原头像
        @unlink( ROOT . 'public'.$_SESSION['avatar']);

        // 设置新头像
        $_SESSION['avatar'] = '/uploads/' . $path;

        message('设置成功', 2, '/blog/index');


   }

  

    public function logout()
    {
        //清空SESSION
        $_SESSION = [];

        //跳转
        // redirect('/');
        message('退出成功', 2, '/');
    }


    public function dologin()
    {
        //接收表单
        $email = $_POST['email'];
        $password = md5($_POST['pass']);
        //模型登录
        $user = new User;
        if($user->login($email,$password))
        {
            // die('登录成功');
            // redirect('/');
            message('登录成功', 2 ,'/blog/index');

        }
        else
        {
            // die('用户名或密码错误');
            // back();
            message('账号或密码错误', 1 ,'/user/login');
        }
    }

    public function login()
    {
        view('users.login');
    }


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
        // $user = new User;
        // $ret = $user->add($email,$password);

        // if(!$ret)
        // {
        //    die('注册失败');
        // }
        

        //生成激活码(随机生成)
        $code = md5( rand(1,99999) );

        $redis = \libs\Redis::getInstance();
        // $redis = new \libs\Redis;
        //序列化
        $value = json_encode([
            'email' => $email,
            'password' => $password,
        ]);
        // 键名
        $key = "temp_users:{$code}";
        $redis->setex($key,180, $value);

        //发激活码到用户邮箱

        //消息队列

         //从邮箱地址取出姓名
        $name = explode('@',$email);
        $from = [$email, $name[0]];

        //构造消息数组
        $message = [
            'title' => '从我是你爹开始',
            'content' => "点击一下连接进行激活:<br><a href='http://localhost:9999/user/activeUser?code={$code}'>
            http://localhost:9999/user/activeUser?code={$code}</a><p>
            若按钮不能点击请复制上面链接地址在浏览器中访问来激活账号</p>",
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

    public function activeUser()
    {
        // 接收激活码
        $code = $_GET['code'];

        //从redis 中取出账号
        $redis = \libs\Redis::getInstance();

        $key = 'temp_users:'.$code;
        //取数据
        $data = $redis->get($key);
        if($data)
        {
            $redis->del($key);
            //反序列化
            $data = json_decode($data, true);

            //插入数据库
            $user = new \models\User;
            $user->add($data['email'], $data['password']);
            // die("激活成功");
            header('Location:/user/login');

        }
        else
        {
            die("激活码无效");
        }
    }
}