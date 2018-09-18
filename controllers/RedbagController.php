<?php
namespace controllers;

class RedbagController
{

    public function robp()
    {
        view('redbag.robp');
    }
    public function init()
    {
        $redis = \libs\Redis::getInstance();
        // 初始库存量
        $redis->set('redbag_stock', 20);
        // 初始空集合
        $key =  'redbag_'.date('Ymd');
        $redis->sadd($key, '-1');
        // 设置过期
        $redis->expire($key, 3900);
    }

    public function makeOrder()
    {
        $redis = \libs\Redis::getInstance();

        $model = new \models\Redbag;

        // 设置永不超时
        ini_set('default_socket_time', -1);
        echo "Start monitoring red packets...\r\n";

        // 循环
        while(true)
        {
            // 队列取数据设置永不超时
            $data = $redis->brpop('redbag_orders', 0);
            echo $data;
            // 处理数据
            $userId = $data[1];
            // 下订单
            $model->create($userId);

            echo "Somebody robbed the red envelope.////.../r/n";
        }

    }

    public function rob()
    {
        // 判断用户有没有登录
        if(!isset($_SESSION['id']))
        {
            echo json_encode([
                'status_code' => '401',
                'message' => '用户未登录',
            ]);
            exit;
        }

        //判断是否在时间内
        if(date('H')<9 || date('H')>20)
        {
            echo json_encode([
                'status_code' => '402',
                'message' => '时间段不允许',
            ]);
            exit;
        }
        
        // 判断今天是否已经抢过
        $key = 'redbag_'.date('Ymd');
        $redis = \libs\Redis::getInstance();
        $exists = $redis->sismember($key, $_SESSION['id']);
        if($exists)
        {
            echo json_encode([
                'status_code' => '403',
                'message' => '今天已经抢过',
            ]);
            exit;
        }

        // 减少库存
        $stock = $redis->decr('redbag_stock');
        if($stock < 0)
        {
            echo json_encode([
                'status_code' => '404',
                'message' => '今天红包已抢完',
            ]);
            exit;
        }

        //下单
        $redis->lpush('redbag_orders',$_SESSION['id']);

        // 把抢过id放到集合数据库中
        $redis->sadd($key, $_SESSION['id']);
        echo json_encode([
            'status_code' => '200',
            'message' => '恭喜抢到',
        ]);

    }
}