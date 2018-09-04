<?php
namespace controllers;

use PDO;
use models\Blog;
class BlogController
{

    // public $pdo;
    public function index()
    {
        
       $blog = new Blog;
       $data = $blog->search();
                

        // echo '<pre>';
        // var_dump($data);
        //加载视图文件
        view('blogs.index', $data);
    }

        //生成日志详情页
    public function contents()
    {
        $blog = new Blog;
        $blog->contents2();
    }

    public function index2Ht()
    {
        $blog = new Blog;
        $blog->indexHt();
    }
}