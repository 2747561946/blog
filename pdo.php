<?php

$host = '127.0.0.1';
$dbname = 'blog';
$user = 'root';
$pass = '123';

$pdo = new PDO("mysql:host={$host};dbname={$dbname}",$user,$pass);
$pdo->exec(' set names utf8 ');

$stmt = $pdo->prepare("insert into blogs('title','content') values(?,?)");
$ret = $stmt->execute([
    '标题xx',
    '内容xx',
]);
if(!$ret)
{
    echo "添加成功";
}
else{
    $error = $stmt->errorInfo();
    echo "失败";
    var_dump($error);
}


//取出前10条
// $stmt = $pdo->query('select * from blogs limit 10');
// var_dump($stmt);
// $data = $stmt->fetch();

// $data = $stmt->fetchAll( PDO::FETCH_ASSOC);

// var_dump( $data );




//**************exec ************ */


/*
for($i=0;$i<100;$i++)
{
    $title = getChar( rand(10,60) );
    $content = getChar( rand(50,300) );
    $pdo->exec("insert into blogs(title,content) value('$title','$content')");
}  

function getChar($num)  // $num为生成汉字的数量
    {
        $b = '';
        for ($i=0; $i<$num; $i++) {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
            $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
            // 转码
            $b .= iconv('GB2312', 'UTF-8', $a);
        }
        return $b;
    }
*/



//插入
// $pdo->exec("insert into blogs(id,title,content) value(null,'标题1','内容1')");

//修改
// $ret = $pdo->exec("update blogs set title='花姑凉',content='你是猪' where id = 1");
// var_dump( $ret );
/*
if($ret === false)
{
    die('出错');
}
var_dump( $ret );

*/
//删除 不重置id
// $pdo->exec("delete from blogs where id =1");


//删除 重置id
// $pdo->exec("truncate blogs");

