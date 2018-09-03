<?php
namespace controllers;

use PDO;

class BlogController
{
    // public $pdo;
    public function index()
    {
        
        //取日志数据
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=blog','root','123');
        $pdo->exec('set names utf8');

        //**************搜索************

        //标题内容搜索
        $where = 1;
        $value = [];
        
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


        //==========排序order=========

        //默认排序
        $orderBy = 'created_at';
        $orderWay = 'desc';

        //排序字段
        if(isset($_GET['order_by']) && $_GET['order_by'] == 'display')
        {
            $orderBy = 'display';
        }
        //排序方式
        if(isset($_GET['order_way']) && $_GET['order_way'] == 'desc')
        {
            $orderWay = 'asc';
        }

        //===========翻页==============

        //每页条数
        $perpage = 10;
        //获取当前页码
        $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
        
        //计算起始值
        $staPage = ($page-1) * $perpage;
        //拼limit
        $limit = $staPage.','.$perpage;

        //===========翻页按钮
        //获取总记录数

        // $allCount = $blogs->count($where);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blogs WHERE $where");
        $stmt->execute($value);

        $allCount = $stmt->fetch(PDO::FETCH_COLUMN);
        // var_dump( $allCount );
        //获取总页数
        $pageCount = ceil($allCount/$perpage);
        // var_dump($pageCount);
        $pageBtn = '';
        for($i=1;$i<$pageCount;$i++)
        {
            $params = getUrl(['page']);
            $pageBtn .= "<a href='?{$params}page=$i'>{$i}</a>";
        }


        // 按钮背景
        $pageBtn = '';
        for($i=1; $i<=$pageCount; $i++)
        {
            // 获取当前除了 page 之前的其它参数
            $urlParams = getUrl(['page']);

            // 为当前页码添加样式
            if($i == $page)
                $class="class='pages";
            else
                $class='';

            $pageBtn .= "<a $class href='?page={$i}{$urlParams}'> {$i} </a>";
        }


        // =========执行sql
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE $where ORDER BY $orderBy $orderWay LIMIT $staPage,$perpage");
        echo "SELECT * FROM blogs WHERE $where ORDER BY $orderBy $orderWay LIMIT $staPage,$perpage";
        $stmt->execute($value);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
   

        // echo '<pre>';
        // var_dump($data);
        //加载视图文件
        view('blogs.index',[
            'data' => $data,
            'pageBtn' => $pageBtn,
        ]);
    }
}