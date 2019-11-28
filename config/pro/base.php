<?php
/**
 * I am what iam
 * Class Descript : 应用级配置，开发环境
 * User: ehtan
 * Date: 2019-10-30
 * Time: 16:29
 */
return [
    'name'  => 'Swoft framework 2.0',
    'debug' => env('SWOFT_DEBUG', env(SWOFT_DEBUG)),
    "defaultPageSize" =>20,//后台列表分页 每页的数目
    //后台相关配置
    "admin"=>[
        'password_sale'=>"e1804fc898317970ad89c69bb13ed03a",//密码盐
    ],

    //websocket 配置
    "wsServer"=>[
        'port'=>8000,
        'auth_hash'=>"qi4ol4fe9a31b970ad89c6dbb13ed000",
        "uri"=>[
            "notice"=>'/notice',//内部消息推送uri
        ]
    ],
    //http配置
    "httpServer"=>[
        'port'=>8000
    ],
    //数据库配置
    'databases' =>[
        'dsn'       => 'mysql:dbname=;host=',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8mb4',
        'prefix'   => 'ads_',
    ],
    //redis配置
    "redis" =>[
        'host'     => '192.168.0.200',
        'driver'   => 'phpredis',
        'port'     => 6379,
        'database' => 0,
        'password' => "",
    ],
    "cache_keys"=>[
        "CACHE_MENU_LIST"=>'MENU_LIST' ,//菜单缓存
    ]
];