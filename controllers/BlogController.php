<?php
namespace controllers;

use PDO;
use models\Blog;
class BlogController
{
    // 显示私有日志
    public function content()
    {
        $id = $_GET['id'];
        $model = new Blog;
        $blog = $model->find($id);

        // 判断是否为自己日志
        if($_SESSION['id'] != $blog['user_id'])
            die('无权访问');
        // 加载视图
        view('blog.content', [
            'blog' => $blog,
        ]);
    }

    public function display()
    {
        $id = (int)$_GET['id'];
        $blog = new Blog;
        $display = $blog->getDisplay($id);
        echo json_encode([
            'display' => $display,
            'email' => isset($_SESSION['email']) ? $_SESSION['email'] : '' ,
        ]);
    }

    public function update()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $id = $_POST['id'];

        // var_dump($id);
        // die;

        $blog = new Blog;
        $blog->update($title,$content,$is_show,$id);

        message('修改成功', 2, '/blog/index');
    }

    public function edit()
    {
        $id = $_GET['id'];
        // 根据id取出日志信息
        $blog = new Blog;
        $data = $blog->find( $id );

        view('blogs.edit', [
            'data' => $data,
        ]);
    }
    public function delete()
    {
        $id = $_POST['id'];
        //
        $blog = new Blog;
        $blog->delete($id);

        // 跳转
        message('删除成功', 2, '/blog/index');
    }

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
/*
    public function display()
    {
        $id = (int)$_GET['id'];

        $blog = new Blog;
        // $display = $blog->getDisplay($id);
        // 把浏览量+1并输出
        echo $blog->getDisplay($id);

       
       
    }
*/
    public function displayAddTo()
    {
        $blog = new Blog;
        $blog->displayAdd();
    }
}