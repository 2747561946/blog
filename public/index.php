<?php

ini_set("date.timezone","Asia/Shanghai");
// 动态的修改 php.ini 配置文件
ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=3');  // 设置 redis 服务器的地址、端口、使用的数据库

session_start(); 

// 如果用POST方式访问网站需要验证令牌
if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if(!isset($_POST['_token']))
        die('违法操作');
    if($_POST['_token'] != $_SESSION['_token'])
        die('违法操作');
}

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

// 过滤xxs
function e($content)
{
    return htmlspecialchars($content);
}

//使用htmlpurifer过滤
function hpe ($content)
{
    //一直保持在内存中(直到脚本执行结束)
    static $purifier = null;
    // 只有第一次才调用创建新对象
    if($purifier === null)
    {
        // 生成配置对象
        $config = \HTMLPurifier_Config::createDefault();
        // 设置编码
        $config->set('Core.Encoding', 'utf-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        // 设置缓存目录
        $config->set('Cache.SerializerPath', ROOT.'cache');
        // 设置允许的 HTML 标签
        $config->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,ol[start],li,p[style],br,span[style],img[width|height|alt|src],*[style|class],pre,hr,code,h2,h3,h4,h5,h6,blockquote,del,table,thead,tbody,tr,th,td');
        // 设置允许的 CSS
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,margin,width,height,font-family,text-decoration,padding-left,color,background-color,text-align');
        // 设置是否自动添加 P 标签
        $config->set('AutoFormat.AutoParagraph', TRUE);
        // 设置是否删除空标签
        $config->set('AutoFormat.RemoveEmpty', true);

        // 3. 
        // 创建对象过滤
        $purifier = new \HTMLPurifier($config);
    }

    
    // 过滤
    $clean_html = $purifier->purify($content);

    return $clean_html;
}

// 生成
function csrf()
{
    if(!isset($_SESSION['_token']))
    {
        // 生成随机令牌
        $token = md5( rand(1,99999) . microtime() );

        $_SESSION['_token'] = $token;
    }
    
    return $_SESSION['_token'];
    
}

// 生成令牌隐藏域
function csrf_field()
{
    // if(!isset($_SESSION['_token']))
    // {
    //     $csrf = $_SESSION['_token'];
    // }
    // else
    // {
    //     $csrf = csrf();
       
    // }

    $csrf = isset($_SESSION['_token']) ? $_SESSION['_token'] : csrf();
    echo "<input type='hidden' name='_token' value='{$csrf}'>";

    
}
