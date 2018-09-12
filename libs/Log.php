<?php
namespace libs;
class Log
{
    private $fp;
    public function __construct($logName)
    {
        //打开日志文件
        $this->fp = fopen(ROOT . 'logs/'.$fileName.'.log','a');
    }

    // 向日志文件中追加内容
    public function log($content)
    {
        fwrite($this->fp, $content . "\r\n");
    }
}