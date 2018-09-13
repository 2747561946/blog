<?php
namespace controllers;

use PDO;
use models\Blog;
class BlogController
{

    // public $pdo;
// 添加日志表单
    public function create()
    {
        view('blogs.create');
    }

    public function store()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        
        $blog = new Blog;
        $blog->add($title,$content,$is_show);

        //跳转
        message('发表成功', 2, '/blog/index');
    }

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

    public function display()
    {
        $id = (int)$_GET['id'];

        $blog = new Blog;
        // $display = $blog->getDisplay($id);
        // 把浏览量+1并输出
        echo $blog->getDisplay($id);

       
       
    }

    public function displayAddTo()
    {
        $blog = new Blog;
        $blog->displayAdd();
;    }
}