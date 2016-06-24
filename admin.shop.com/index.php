<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

//定义一个项目目录常量
define('ROOT_PATH',  __DIR__ . '/');

// 定义应用目录
define('APP_PATH',ROOT_PATH . '/Application/');

//创建模块
// 此常量有以下作用
//1.如果没有该模块,就创建 2.如果有不做任何事,但是有了这行代码,那么m参数就会失效
//3.所以创建好后注释掉这行代码
//define('BIND_MODULE','Admin');

//定义单模块模式，不用输模块名，直接输入控制器和方法名即可显示

// 引入ThinkPHP入口文件
require dirname(ROOT_PATH) .'/ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单