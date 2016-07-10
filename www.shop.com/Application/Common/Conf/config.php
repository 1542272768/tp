<?php
define('BASE_URL','http://www.shop.com');
return array(
	//'配置项'=>'配置值'

    //设置模版的引入地址
    'TMPL_PARSE_STRING'=>[
        '__CSS__'=>BASE_URL.'/Public/Home/css',
        '__IMG__'=>BASE_URL.'/Public/Home/images',
        '__JS__'=>BASE_URL.'/Public/Home/js',
        '__UPLOADIFY__'=>BASE_URL.'/Public/Home/ext/uploadify',
        '__LAYER__'=>BASE_URL.'/Public/Home/ext/layer',
        '__ZTREE__'=>BASE_URL.'/Public/Home/ext/ztree',
        '__TREEGRID__'=>BASE_URL.'/Public/Home/ext/treegrid',
        '__UEDITOR__'=>BASE_URL.'/Public/Home/ext/ueditor',
        '__JQUERY_VALIDATE__'=>BASE_URL.'/Public/Home/ext/jquery-validate',
    ],
    //设置单模块模式
    define('BIND_MODULE','Home'),

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
        ],
    ],
    //cookie加前缀
    'COOKIE_PREFIX'         =>  'www_shop_com_',

    //Session配置
    'SESSION_AUTO_START'	=>  true,	// 是否自动开启Session
    'SESSION_TYPE'			=>  'Redis',	//session类型
    'SESSION_PERSISTENT'    =>  1,		//是否长连接(对于php来说0和1都一样)
    'SESSION_CACHE_TIME'	=>  1,		//连接超时时间(秒)
    'SESSION_EXPIRE'		=>  0,		//session有效期(单位:秒) 0表示永久缓存
    'SESSION_PREFIX'		=>  'sess_',		//session前缀
    'SESSION_REDIS_HOST'	=>  '127.0.0.1', //分布式Redis,默认第一个为主服务器
    'SESSION_REDIS_PORT'	=>  '6379',	       //端口,如果相同只填一个,用英文逗号分隔
    'SESSION_REDIS_AUTH'    =>  '',    //Redis auth认证(密钥中不能有逗号),如果相同只填一个,用英文逗号分隔

    //页面静态化配置
    'HTML_CACHE_ON' => false,        // 开启静态缓存
    'HTML_CACHE_TIME' => 60,        // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX' => '.shtml', // 设置静态缓存文件后缀,页面有shtml即为静态页面
    'HTML_CACHE_RULES' => array(    // 定义静态缓存规则
        // 定义格式1:数组方式   'Index:index' => array('静态规则','有效期','附加规则'),
        //Index控制器:下的所有方法实现静态化=>实现的的命名规则是控制器名_方法名_id值(id用于解决静态页面访问的问题)
        'Index:' => array('{:controller}_{:action}_{id}'),
        // 定义格式2 字符串方式  '静态地址' => '静态规则',
    ),

    //开启数据缓存存入redis机制
    'DATA_CACHE_TYPE'=>'Redis',
    'REDIS_HOST'=>'127.0.0.1',
    'REDIS_PORT'=>6379,


);