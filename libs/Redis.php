<?php
namespace libs;

class Redis
{
    private static $redis = null;
    private function __clone(){}
    private function __construct(){}


     //获取redis 对象
    public static function getInstance()
    {
        //从配置文件读取账号
        $config = config('redis');
        if(self::$redis === null)
        {
            
            self::$redis = new \Predis\Client($config);
        }
        return self::$redis;
    }

   
   
}