<?php

// 调试模式
define('APP_DEBUG', true);

// 网站入口路径
define('ROOT_PATH', dirname( __FILE__ ) . '/');

// 加载框架基础引导文件
require dirname( __FILE__ ) . '/includes/start.php';
start::run();
?>
