<p align="center">
    <a href="https://github.com/swoft-cloud/swoft" target="_blank">
        <img src="http://qiniu.daydaygo.top/swoft-logo.png?imageView2/2/w/300" alt="swoft"/>
    </a>
</p>



### swoft Xadmin 
基于swoft 2.x 和 xadmin 开发的快速后台框架。

##### 版本和依赖要求：
* php 7.2+
* swoole 4.4+
* php-redis拓展
* linux或macos系统

##### 参考文档
[swoole文档](https://wiki.swoole.com/)  
[swoft文档](https://swoft.org/docs)  
[xadmin演示](http://x.xuebingsi.com/)  
 
# 升级记录

|  版本   | 更新内容  |
|  ----  | ----  |
| v0.0.1  |  |
| v0.0.2  | 删除一个特别智障的问题 | 
| v0.0.3  | 修复一些特别蛋疼的问题 | 
| v0.1.0  | 修复一些无聊的问题，使用官方session组件 | 

# 安装方法
* 1、修改```./config/dev(pro)/bese.php```中关于数据库的配置  
* 2、在mysql中新建一个数据库  
* 3、composer install
* 4、执行```php bin/swoft install:exec```，如果数据库中出现表说明安装成功  
* 5、启动框架访问

# 启动方式

```
    swoftcli run -c ws:start
```
或者
```
    php bin/swoft ws:start
```

## 切换配置

应用配置位于 ```./config/{ENV}/base.php```  
切换环境配置只需要将```./app/base.php```文件中的配置修改为：
```php
//应用级配置
    'config'   => [
        'path' => __DIR__ . '/../config',
        'env' =>'pro' //pro为生产环境配置，dev为开发环境配置
    ],
```

# 分组路由

支持分组路由，如 ```http://DOMAIN/admin/auth.group/lists```，对应目录: ```Http/Controller/Admin/Auth/AdminController```


# 快速curd模板

内置快速curd操作，可以方便快捷的搭建出常用的curd功能。支持： 
* lists 分页列表
* add 添加
* edit 修改
* del 删除
* 快捷导出到excel

## 使用要求

* 控制器的名称必须与路由名称对应，如： ```IndexController.php``` 路由地址必须是 ```/admin/index```；
* 提供各种重写方法，以 ```__foo```格式重写，返回指定的格式；
* 模板引擎使用THINKPHP，文档地址： [think-template](https://www.kancloud.cn/manual/think-template/1286403)
## 路径定义

```url
http://DOMAIN/admin/{Controller}/{lists/add/edit/del}/{?id=}
```
```Controller``` 为你的控制器名称,路由将自动寻找
```controller/{lists/add/edit/del}```模板文件 如:```http://DOMAIN/admin/index/lists``` 
，路由器会寻找```/resource/views/index/lists.html```模板文件，否则将会抛出异常到控制台。

## 全局配置

在需要使用快速curd模板的控制器中引入Trait（trait引入方式见php document)即可
```php
use \App\Http\Traits\HttpBaseTrait;
```

### 关联实体模型

```php
protected $model ={Entity}::class;
```
```Entity``` :你需要关联的实体名称:如  
```php 
protected $model =Admin::class; 
```

## 一、list列表模板

```url
http://DOMAIN/admin/{Controller}/lists
```
lists 分页模板会查找你绑定的实体内容并将内容分页输出。
lists将会输出以下变量：
* ``` $list ```： 格式化后的分页数据
* ``` $keywords ```: 查询的条件名称
* ``` $page ```; html分页样式（前端模板使用原生方式输出 ```{:$page}``` )


### lists快捷模板

##### 一、模糊搜索

```html
<div class="layui-input-inline">
    <input type="text" name="params[like][value]" lay-verify="title" autocomplete="off" placeholder="请输入关键词" class="layui-input layui-btn-sm" value="<?php echo($data['keywords']['like']['value']); ?>">
    <input name="params[like][fields]" value="{field1}|{field2}" type="hidden">
    <input type="submit" >
</div>
```
* ```params[like][value]```和```params[like][fields]```为固定模板查询name值
* ```{field1}|{field2}```替换成你需要查询的数据库的表字段（必须与表字段对应），使用管道符"|" 分割。

##### 二、精确搜索

```html
 <input type="input" name="params[{fields}]" value="{your values}">
```
* ```name="params[{fields}]"``` fields替换成你需要查询的字段名称; 
* ```value="{your values}"``` your values为你需要搜索的值

#### 重写方法

##### 一、 重写请求查询条件方法

用于重写查询条件，返回条件数组
```php
__listsKeywords(array $params, array $map):array
```
* ```array $param``` 通过``` \context()->getRequest()->input();``` 获取的请求参数。
* ```array $map``` 组装好的符合swoft的ORM查询条件数组
* 必须返回符合swoft的ORM查询条件数组！

##### 二、重写查询语句

用于定制特殊查询语句，返回查询结果
```php
__listsData(array $map,array $params):array
```
* ```array $map``` 符合swoft的ORM查询条件数组
* ```array $params``` 通过```\context()->getRequest()->input()```获取的参数

* 返回查询的结果的数组

##### 三、重写渲染页面

用于自行渲染页面，返回标准响应结果
```php
__listsDisplay(array $buildData):Response
```
* ```array $buildData``` 包含```array $list```查询结果，```array $keywords```请求参数数组，```string $page```分页html样式
* 返回```Swoft\Http\Message\Response``` 对象

## 二、add\edit快捷模板

```url
http://DOMAIN/admin/{Controller}/{add/edit}{/?id=}
```

### 2.1 添加页面

```url
http://DOMAIN/admin/{Controller}/add
```
* 模板文件名必须为```info.html``` 。

##### 一、重写添加渲染页面

```php
__addDisplay():Response
``` 
* 重写渲染页面，返回```Swoft\Http\Message\Response``` 对象

```php
__addUpdateBefore(array $requestParams):bool
```
* 重写插入数据方法，用于自行写入数据库操作
* 返回```boolean```值用于判断是否执行成功

### 2.2 修改页面

```url
http://DOMAIN/admin/{Controller}/edit/?id={index_id}
```
* 模板文件名必须为```info.html``` 。
* 请求id必须为主键id


```php
__editUpdateBefore(array $requestParams):bool
```
* 重写插入数据方法，用于自行更新数据库操作
* 返回```boolean```值用于判断是否执行成功

##### 一、重写修改渲染页面

```php
__editDisplay(array $info):Response
``` 
* ```array $info```主键查询结果数组。
* 重写渲染页面，返回```Swoft\Http\Message\Response``` 对象


### 2.3 添加/修改数据

#### 添加数据

```url
http://DOMAIN/admin/{Controller}/add
```
添加数据模板name字段必须跟mysql表字段对应。

#### 修改数据

```url
http://DOMAIN/admin/{Controller}/edit
```
修改数据必须提交带有主键ID的值

#### 添加/修改前置方法

```php
__{add/edit}Before(array $request):array
```
* ```array $request``` 提交的参数值
* 必须返回```array```类型参数

#### 添加/修改后置方法

```php
__{add/edit}After(Mix $result):Response
```
* ```mix $result``` 数据库提交、更新结果
* 必须返回```Swoft\Http\Message\Response```对象

## 三、快捷删除

```url
http://DOMAIN/admin/{Controller}/del{/?id=}
```
* id参数可以get/post提交，id为表主键
* 该方法为硬删除（不保留数据）

#### 前置方法

```php
__delBefore(array $map):$map
```
* ```array $map```符合swoft ORM条件的数组
* 返回符合swoft ORM条件的数组

#### 后置方法

```php
__delAfter(Mix $result):Response
```
* ```mix $result``` 数据库提交、更新结果
* 必须返回```Swoft\Http\Message\Response```对象


## 四、导出到excel

添加导出按钮
```html
 <button class="layui-btn layui-btn-normal" onclick="outputExcel('{:getOutputUri()}')">
    <i class="layui-icon">&#xe601;</i> 导出到excel
 </button>
```
### 配置

在需要导出的类中添加配置
```php
    protected $output=[
      ["字段名称","excel标题名称",{['为true字段','为false字段']}],
    ];
```
* 第1个值为在mysql中的字段名称
* 第2个字为在excel中标题的名称（需要跟第一个值一一对应）
* 第3个为选填值，该值为条件值，需要修改字段在excel中的展示值（如 status=1 ，那么第三个字段写成['开启', '禁用'])

#### eg

```php
    protected $output=[
      ["admin_id","id"],
      ["admin_username","用户名"],
      ["created_at","创建时间"],
      ["admin_bs","状态",['启用','禁用']],
      ["admin_nickname","昵称"],
      ["group_name","所属组"],
    ];
```



Swoft is an open-source software licensed under the [LICENSE](LICENSE)

power by ethan[touch_789@163.com] [LICENSE](LICENSE)
