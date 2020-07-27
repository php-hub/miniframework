<?php
if( APP_DEBUG === true){
    define("DB_HOST",     "127.0.0.1");
    define("DB_NAME",     "winnie");
    define("DB_USER",     "root");
    define("DB_PASSWORD", "root");
}else{
    define("DB_HOST",     "127.0.0.1");
    define("DB_NAME",     "pqjob");
    define("DB_USER",     "root");
    define("DB_PASSWORD", "Yuyu2017"); 
}

define("DB_PREFIX",   "pqsystem_");

// URL访问模式, 0 (普通模式); 1 (REWRITE  模式);
define('URL_MODEL',          0);
define('URL_HTML_SUFFIX',    'html');  // URL伪静态后缀设置
define('URL_PATHINFO_FETCH', 'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL'); // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
define('URL_PATHINFO_DEPR',  '/');     // PATHINFO模式下，各参数之间的分割符号

define('MODULE_ALLOW_LIST',  'home,admin'); // 合法的模块
define('DEFAULT_MODULE',     'home');       // 默认模快
define('TEMP_SUFFIX',        '.php');       // 模板文件格式


// 系统应用目录
define( 'APP_PATH',          ROOT_PATH . 'application/' );
define( 'INC_PATH',          ROOT_PATH . 'includes/' );
define( 'RUNTIME_PATH',      ROOT_PATH . 'content/runtime/' );
define( 'UPLOAD_PATH',       ROOT_PATH . 'content/uploads/' );

// 模板资源路径
define("__ROOT__",           '/pqsystem/');              // 网站入口目录，如果是根目录则 "/"。
define("__CONTENT__",        __ROOT__ . 'content/');     // 内容目录
define("__UPLOADS__",        __CONTENT__ . 'uploads/');  // 文件上传目录

// 后台资源路径
define("__ADMIN__",         __ROOT__ . 'application/admin/view/assets');

// 身份认证密钥
define('ADMIN_USER_KEY',     'PQsystem_sasa');

?>
