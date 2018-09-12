<?php

// 动态的修改 php.ini 配置文件
ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=3');  // 设置 redis 服务器的地址、端口、使用的数据库

session_start(); 




//定义常量
define('ROOT',dirname(__FILE__) . '/../');

//引入composer自动加载文件
require(ROOT.'vendor/autoload.php');
// require(ROOt. 'controller/UserController.php');
//实现自动加载
function autoload($class)
{
    // controller\UserController
    //替换
    $path = str_replace('\\','/',$class);

    // echo Root . $path . '.php';
    // die;

    require(ROOT . $path . '.php');
}

spl_autoload_register('autoload');
// $user = new controller\UserController;

//添加路由
//获取URL上的路径
// blog/index    浏览器
// blog index    Cli
if(php_sapi_name() == 'cli')
{
    $controller = ucfirst($argv[1]) . 'Controller';
    $action = $argv[2]; 
}else
{
    if(isset($_SERVER['PATH_INFO']) )
    {
        $pathInfo = $_SERVER['PATH_INFO'];
        $pathInfo = explode('/', $pathInfo);


        $controller = ucfirst($pathInfo[1]) . 'controller';
        $action = $pathInfo[2];
    }
    else{
        //默认控制器
        $controller = 'IndexController';
        $action = 'index';
    }

}



// echo '<pre>';
// var_dump( $pathInfo );
// die;

//为类添加命名空间
$fullController = 'controllers\\'.$controller;

$U = new $fullController;
$U->$action();

function view($viewFileName,$data = [])
{
    extract($data); //解压$data
    // user.hello = user/hello.html
    $path = str_replace('.','/',$viewFileName) . '.html';

    //加载视图
    require(ROOT . 'views/' . $path);
}


function getUrl($par=[])
{
    $ret = '';
    foreach($par as $k => $v){
      
        unset($_GET[$v]);
    }

   
    
    foreach($_GET as $k => $v)
    {
        $ret .= "&$k=$v";
    }
    return $ret;
}

//获取配置文件
//无论调用多少次 只包含一次配置文件
// 静态局部变量 函数执行结束也不会销毁 一直存在到整个脚本结束
// 普通局部变量 函数执行完就销毁
function config($name)
{
    static $config = null;
    if($config === null)
    {
        $config = require(ROOT.'config.php');
    }
    

    return $config[$name];
}


function redirect($url)
{
    header('Location:' . $url);
    exit;
}

//跳回上一个页面
function back()
{
    redirect($_SERVER['HTTP_REFERER']);
}

// echo "<pre>";
// var_dump($_SERVER);

//操作成功

function message($message, $type, $url, $seconds = 5)
{
    if($type == 0)
    {
        echo "<script>alert('{$message}');location.href='{$url}';</script>";
        exit;
    }
    else if($type == 1)
    {
        //加载消息页面
        view('common.success' , [
            'message' => $message,
            'url' => $url,
            'seconds' => $seconds
        ]);
    }
    else if($type == 2)
    {
        $_SESSION['_MESS_'] = $message;
        redirect($url);
    }
}
//操作失败

