<?php

use Swoft\Http\Server\HttpServer;

return [
    //应用级配置
    'config'   => [
        'path' => __DIR__ . '/../config',
        'env' =>'dev'
    ],
    'logger'     => [
        'flushRequest' => false,
        'enable'       => false,
        'json'         => false,
    ],
    'httpServer' => [
        'class'   => HttpServer::class,
        'port' => config("httpServer.port"),
    ],
    //websocket处理
    'wsServer'   => [
        'port'  => config("httpServer.port"),
        'class'   => \Swoft\WebSocket\Server\WebSocketServer::class,
        'debug' => env('SWOFT_DEBUG', 0),
        /* @see WebSocketServer::$setting */
        'setting' => [
            // enable static handle
            'enable_static_handler'    => true,
            // swoole v4.4.0以下版本, 此处必须为绝对路径
            'document_root' => dirname(__DIR__) . '/public',
            'heartbeat_check_interval' => 5,
            'heartbeat_idle_time' => 10,
            'worker_num' => 4,
            'log_file' => alias('@runtime/swoole.log'),
            //task任务
            'task_worker_num'       => 2,
            'task_enable_coroutine' => true
        ],
        'on'      => [
            // 加上如下一行，开启处理http请求
            \Swoft\Server\SwooleEvent::REQUEST => bean(\Swoft\Http\Server\Swoole\RequestListener::class),
            // Enable task must task and finish event
            \Swoft\Server\SwooleEvent::TASK   => \bean(\Swoft\Task\Swoole\TaskListener::class),
            \Swoft\Server\SwooleEvent::FINISH => \bean(\Swoft\Task\Swoole\FinishListener::class)
        ],
    ],

    //中间件
    'httpDispatcher'=>[
        //全局中间件
        'middlewares'=>[
            \Swoft\Http\Session\SessionMiddleware::class,//session中间件
            \App\Http\Middleware\httpRouteMiddleware::class, //http请求全局中间件
        ],
        'afterMiddlewares' => [

        ]
    ],
    //数据库配置
    'db'         => [
        'class'     => \Swoft\Db\Database::class,
        'dsn'       => config("databases.dsn"),
        'username'  => config("databases.username"),
        'password'  => config("databases.password"),
        'charset'   => config("databases.charset"),
        'prefix'   =>  config("databases.prefix"),
    ],
    'db.pool' => [
        'class'       => \Swoft\Db\Pool::class,
        'database'    => bean('db'),
        'minActive'   => 2,
        'maxActive'   => 20,
        'maxWait'     => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 60,
    ],
    //redis配置
    'redis'      => [
        'class'    => \Swoft\Redis\RedisDb::class,
        'driver'   => config("redis.driver"),
        'host'     => config("redis.host"),
        'port'     => config("redis.port"),
        'database' => config("redis.database"),
        'password' => config("redis.password"),
    ],
    'redis.pool'     => [
        'class'   => \Swoft\Redis\Pool::class,
        'redisDb' => \bean('redis'),
        'minActive'   => 10,
        'maxActive'   => 20,
        'maxWait'     => 0,
        'maxWaitTime' => 0,
        'maxIdleTime' => 60,
    ],
];
