<?php
//核心启动类
class start {
  //run方法
  public static function run() {
      self::init();
      self::autoload();
      self::dispatch();
  } 
  //初始化方法
  private static function init() {
    // 系统信息
    if(version_compare(PHP_VERSION,'5.4.0','<')) {
      die("PHP版本 < 5.4.0 无法使用本系统");
    }
    define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
    define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
    define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);

    //开启session
    session_start();

    // 加载配置文件
    include_once ROOT_PATH  . "config/config.php";
    
    // URL解析、路由和调度
    include_once ROOT_PATH . 'includes/core/Dispatcher.php';
    new dispatcher();

    // 加载公用方法
    include_once ROOT_PATH . 'includes/function.php';

    // 加载核心类
    include_once ROOT_PATH . 'includes/core/Controller.php';
    include_once ROOT_PATH . 'includes/core/Model.php';
  }

  //自动载入类
  private static function autoload() {
      spl_autoload_register('self::loadfile');
  }

  // 自动加载器
  private static function loadfile($class){
      $file = self::findFile($class);
      if (file_exists($file)) {
          if (is_file($file)) {
            include_once $file;  // 引入文件
          }
      }
  }

  // 解析文件路径
  private static function findFile($class){
    // 命名空间路径映射
    $vendorMap = [
      'app'       => APP_PATH,
      'core'      => INC_PATH.'core',
      'extend'    => INC_PATH.'extend',
    ];
    $vendor = substr($class, 0, strpos($class, '\\'));                  // 顶级命名空间
    $vendorDir = isset($vendorMap[$vendor])?$vendorMap[$vendor]:$class; // 文件基目录
    $filePath = substr($class, strlen($vendor)) . '.php';               // 文件相对路径
    return strtr($vendorDir . $filePath, '\\', DIRECTORY_SEPARATOR);    // 文件标准路径
  }


  //路由分发，即实例化对象调用方法
  private static function dispatch() {
    $module_name = MODULE_NAME;
    $controller_name = ucfirst(CONTROLLER_NAME);
    $action_name = ACTION_NAME;

    //echo '<pre>Module:'.$module_name .'</pre>';
    //echo '<pre>Controller:'.$controller_name.'</pre>';
    //echo '<pre>Action:'.$action_name.'</pre>';

    // 载入类文件
    $classfile = APP_PATH . $module_name . '/controllers' . "/" . $controller_name.".php";
    if(file_exists($classfile)){
      include_once $classfile;
    }else{
      error('控制器不存在');
    }

    //实例化对象 home\controllers
    $controller = '\\app\\'.$module_name.'\\controllers\\'. $controller_name;
    if(class_exists($controller)){
      $controller = new $controller;
    }else{
      error('找不到控制器');
    }
    //调用方法
    if(method_exists($controller,$action_name)){
      $controller->$action_name();
    }else{
      error('找不到方法');
    }
  }
}
