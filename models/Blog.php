<?php
namespace models;
use PDO;
use libs\Rerdis;
class Blog extends Base
{
    public function add($title,$content,$is_show)
    {
        $stmt = self::$pdo->prepare("INSERT INTO blogs(title,content,is_show,user_id) VALUES(?,?,?,?)");
        
        $ret = $stmt->execute([
            $title,
            $content,
            $is_show,
            $_SESSION['id'],
        ]);
            if(!$ret)
            {
                echo '失败';
                $error = $stmt->errorInfo();
                echo "<pre>";
                var_dump($error);

            }
        // 返回
        return self::$pdo->lastInsertId();
    }

     //**************搜索************
    public function search()
    {
        
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
        $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM blogs WHERE $where");
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
        $stmt = self::$pdo->prepare("SELECT * FROM blogs WHERE $where ORDER BY $orderBy $orderWay LIMIT $staPage,$perpage");
        // echo "SELECT * FROM blogs WHERE $where ORDER BY $orderBy $orderWay LIMIT $staPage,$perpage";
        $stmt->execute($value);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return [
            'pageBtn' => $pageBtn,
            'data' => $data,
        ];
   
    }

    public function contents2()
    {
        // 取日志的数据
        // $pdo = new PDO('mysql:host=127.0.0.1;dbname=blog', 'root', '123');
        // $pdo->exec('SET NAMES utf8');

        $stmt = self::$pdo->query('SELECT * FROM blogs');
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 开启缓冲区
        ob_start();

        // 生成静态页
        foreach($blogs as $v)
        {
            // 加载视图
            view('blogs.content', [
                'blog' => $v,
            ]);
            // 取出缓冲区的内容
            $str = ob_get_contents();
            // 生成静态页
            file_put_contents(ROOT.'public/contents/'.$v['id'].'.html', $str);
            // 清空缓冲区
            ob_clean();
        }
    }

    public function indexHt()
    {
        $stmt = self::$pdo->query("SELECT * FROM blogs WHERE is_show=1 ORDER BY id DESC LIMIT 20");
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //开启缓冲区
        ob_start();
        //加载视图
        view('index.index',[
            'blogs' => $blogs,
        ]);
 
        //从缓冲区取出页面
        $str = ob_get_contents();
        //吧页面内容写到静态页
        file_put_contents(ROOT.'public/index.html',$str);
    }

    //从数据库中取出日志浏览量
    public function getDisplay($id)
    {
        $key = "blog-{$id}";

        $redis = \libs\Redis::getInstance();

        if($redis->hexists('blog_displays', $key))        
        {
            // 累加 并且返回添加之后的值
            // hincrby  把值加1 
            $newNum = $redis->hincrby('blog_displays', $key, 1);
            return $newNum;
        }
        else{

            // echo $key;
          

            $stmt = self::$pdo->prepare('SELECT display FROM blogs WHERE id=?');
            // echo "SELECT display FROM blogs WHERE id=2";
            $stmt->execute([$id]);
            // var_dump($id);
            $display = $stmt->fetch( PDO::FETCH_COLUMN );

            $display++;
            //加到redis
            // hset保存到redis
            $redis->hset('blog_displays', $key, $display);
            return $display;
            // echo $display;
        }

        
    }

    //把内存中的浏览量写到数据库中
    public function displayAdd()
    {
        //1 取出内存浏览量
        $redis = new \Predis\Client([
            'scheme' =>'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);

        $data = $redis->hgetall('blog_displays');
            // var_dump($data);
            
        //2 更新回数据库

        foreach($data as $k => $v)
        {
            $id = str_replace('blog-', '', $k);
            $sql = "UPDATE blogs SET display={$v} WHERE id = {$id}";
            
            self::$pdo->exec($sql);

        }
        echo $data;
        echo "UPDATE blogs SET display={$v} WHERE id = {$id}";
    }
    
}