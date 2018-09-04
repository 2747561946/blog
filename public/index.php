<?php
/*
// 动态的修改 php.ini 配置文件
ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=3');  // 设置 redis 服务器的地址、端口、使用的数据库

session_start(); 

*/


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

