<?php
namespace controllers;

use PDO;

class BlogController
{
    public function index()
    {
        //取日志数据
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','123');
        $pdo->exec('set names utf8');

        //**************搜索************

        //标题内容搜索
        $where = 1;
        $value = [];
        // if(isset($_GET['keyword']) && $_GET['keyword'])
        // {
        //     // $where .= ' AND title like "%'.$_GET['keyword'].'%" or content like "%'.$_GET['keyword'].'%"';
        //     $where .= " AND (title LIKE '%'.?.'%' ) ";
        //     $value[] = $_GET['keyword'];
           
        // }


        if(isset($_GET['keyword']) && $_GET['keyword'])
        {
            $where .= " AND (title LIKE ?)";
            
            $value[] = '%'.$_GET['keyword'].'%';
           
        }



        //开始时间
       
        if(isset($_GET['start_date']) && $_GET['start_date'])
        {
            $where .= " AND created_at >= ?";
            $value[] = $_GET['start_date'];
        }
        //结束时间
        if(isset($_GET['end_date']) && $_GET['end_date'])
        {
            $where .= " AND created_at <= ?";
            $value[] = $_GET['end_date'];
           
        }
        //是否显示
        // if(isset($_GET['is_show']) && $_GET['is_show']==1 || $_GET['is_show'] ==='0')
        if(isset($_GET['is_show']) && ($_GET['is_show']==1 || $_GET['is_show']==='0'))
        {
            $where .= " AND is_show = ?";
            $value[] = $_GET['is_show'];
        }


        // 执行sql
        $stmt = $pdo->prepare("select * from blogs where $where");
        // echo "select * from blogs where $where";
        $stmt->execute($value);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
   

        // echo '<pre>';
        // var_dump($data);
        //加载视图文件
        view('blogs.index',[
            'data' => $data,
        ]);
    }
}