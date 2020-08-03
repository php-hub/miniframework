<?php

// 载入调试配置
if( file_exists("debug.php") ){
    include_once("debug.php");
}

// 数据库配置
if(!defined("DB_HOST"))     define("DB_HOST","127.0.0.1");
if(!defined("DB_NAME"))     define("DB_NAME","test");
if(!defined("DB_USER"))     define("DB_USER","root");
if(!defined("DB_PASSWORD")) define("DB_PASSWORD","root");
if(!defined("DB_PREFIX"))   define("DB_PREFIX","pq_");


// URL访问配置;
define('URL_MODEL', 1); // URL访问模式 0 (普通模式); 1 (REWRITE  模式);
define('URL_PATHINFO_FETCH', 'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL'); // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
define('URL_HTML_SUFFIX',    'html');      // 后缀
define('MODULE_ALLOW_LIST',  'home,admin'); // 允许访问模块
define('DEFAULT_MODULE',     'home');       // 默认模快
define('DEFAULT_CONTROLLER', 'index');      // 默认控制器
define('DEFAULT_ACTION',     'index');      // 默认方法

// 系统应用目录
define( 'APP_PATH',          ROOT_PATH . 'application/' );
define( 'INC_PATH',          ROOT_PATH . 'includes/' );
define( 'CONTENT',           ROOT_PATH . 'content/' );
define( 'RUNTIME_PATH',      CONTENT   . 'runtime/' );

// 模板资源路径
define("__ROOT__",           '/pqsystem/');              // 网站入口目录，如果是根目录则 "/"。
define("__CONTENT__",        __ROOT__ . 'content/');     // 内容目录
define("__UPLOADS__",        __CONTENT__ . 'uploads/');  // 文件上传目录

?>
