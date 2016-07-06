<?php
define('BASE_URL','http://admin.shop.com');
return array(
	//'配置项'=>'配置值'

    //设置模版的引入地址
    'TMPL_PARSE_STRING'=>[
        '__CSS__'=>BASE_URL.'/Public/Admin/css',
        '__IMG__'=>BASE_URL.'/Public/Admin/images',
        '__JS__'=>BASE_URL.'/Public/Admin/js',
        '__UPLOADIFY__'=>BASE_URL.'/Public/Admin/ext/uploadify',
        '__LAYER__'=>BASE_URL.'/Public/Admin/ext/layer',
        '__ZTREE__'=>BASE_URL.'/Public/Admin/ext/ztree',
        '__TREEGRID__'=>BASE_URL.'/Public/Admin/ext/treegrid',
        '__UEDITOR__'=>BASE_URL.'/Public/Admin/ext/ueditor',
    ],
    //设置单模块模式
    define('BIND_MODULE','Admin'),

    /* 数据库设置 */
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'tp',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '123',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  '',    // 数据库表前缀
    'DB_PARAMS'          	=>  array(), // 数据库连接参数
    'DB_DEBUG'  			=>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        =>  0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        =>  false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           =>  '', // 指定从服务器序号

    /* URL设置 */
    'URL_CASE_INSENSITIVE'  =>  true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             =>  2,       // URL访问模式,可选参数0123,2代表(REWRITE模式)

    //分页的配置文件
    'PAGE_SETTING'=>[
        'PAGE_SIZE'=>2,//分页页数
        //分页样式
        'PAGE_THEME'=>'%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
    ],

    //上传文件插件UPLOADIFY
    'UPLOAD_SETTING'=>  [
        'rootPath'     => ROOT_PATH,
        'savePath'     => 'uploads/',
        'mimes'        => array(), //允许上传的文件MiMe类型
        'maxSize'      => 0, //上传的文件大小限制 (0-不做限制)
        'exts'         => array(), //允许上传的文件后缀
        'autoSub'      => true, //自动子目录保存文件
        'subName'      => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'saveName'     => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'      => '', //文件保存后缀，空则使用原后缀
        'replace'      => false, //存在同名是否覆盖
        'hash'         => true, //是否生成hash编码
        'callback'     => false, //检测文件是否存在回调，如果存在返回文件信息数组

        'driver'       => '', // CDN文件上传驱动'Qiniu'
//        'driverConfig' => array(
//            'secretKey' => 'NMZ_hf9zIEsUJ3z5l6r-SaykwwFRE6zFWJfrdjyU', //SK   要看tp/ThinkPHP/Library/Think/Upload/Driver/Qiniu.class.php中
//            'accessKey'  => '5Cj8JsbjPH0amQqVY58Wbq5YxM62iBJL40AWucwE', //AK
//            'domain'     => 'o9helspjr.bkt.clouddn.com', //域名
//            'bucket'     => 'tp0330', //空间名称
//            'timeout'    => 300, //超时时间
//        ), // 上传驱动配置
    ],

    //行为忽略列表
    'ACCESS_IGNORE'=>[
        'IGNORE'=>[  //所有人都可见的页面
            'Admin/Admin/login',
            'Admin/Captcha/captcha',
        ],
        'USER_IGNORE'=>[    //登陆后都可见的页面
             'Admin/Index/index',
             'Admin/Index/top',
             'Admin/Index/menu',
             'Admin/Index/main',
             'Admin/Admin/logout',
             'Admin/Admin/changePassword',
             'Admin/Admin/reset',
        ],
    ],
    //cookie加前缀
    'COOKIE_PREFIX'         =>  'admin_shop_com_',


);