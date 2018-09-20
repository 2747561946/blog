<?php
namespace libs;
class Uploader
{
    // ===单例模式
    private function __construct(){}
    private function __clone(){}
    private static $_obj = null;
    public static function make()
    {
        if( self::$_obj === null)
        {
            // 生成d对象
            self::$_obj = new self;
        }
        return self::$_obj;
    }

    // 定义属性
    private $_root = ROOT . 'public/uploads/';
    private $_ext = ['image/jpeg','image/jpg','image/ejpeg','image/png','image/gif','image/bmp'];
    private $_maxSize = 1024*1024*1.8;
    private $_file;
    private $_subDir;

    // 定义公开方法
    public function upload($name, $subdir)
    {
        // 把用户图片信息保存到属性上
        $this->_file = $_FILES['avatar'];
        $this->_subDir = $subDir;
        // var_dump($this->_file);


        // var_dump($this->_checkType());
        // die;
        if(!$this->_checkType())
        {
            die('图片类型不正确');
        }

        if(!$this->_checkSize())
        {
            die('图片尺寸不正确');
        }

        // 创建目录
        $dir = $this->_makeDir();
        // 生成唯一名字
        $name = $this->_makeName();
        //移动图片
        move_uploaded_file($this->_file['tmp_name'],$this->_root.$dir.$name);
        // 返回二级目录开始路径
        return  $dir.$name;
    }

    // 定义私有方法
    // 创建目录
    public function _makeDir()
    {
        $dir = $this->_subDir . '/' . date('Ymd');
        if(!is_dir($this->_root . $dir))
            mkdir($this->_root . $dir, 007, TRUE);
        
        return $dir.'/';
    }
    // 生成唯一名字
    public function _makeName()
    {
        $name = md5( time() . rand(1,9999) );
        $ext = strrchr($this->_file['name'], '.');
        return $name .$ext;
    }

    private function _checkType()
    {
        return in_array($this->_file['type'], $this->_ext);
    }

    private function _checkSize()
    {
        return $this->_file['size'] < $this->_maxSize;
    }
}