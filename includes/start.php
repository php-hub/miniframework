<?php
//核心启动类
class start {

  //run方法
  public static function run() {
      self::setReporting();
      self::init();
      self::autoload();
      self::dispatch();
  } //初始化方法
  private static function init() {
    // 系统信息
    if(version_compare(PHP_VERSION,'5.4.0','<')) {
      die("PHP版本 < 5.4.0 无法使用本系统");
    }
    define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
    define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
    define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);

    //载入配置文件
    include ROOT_PATH  . "/config.php";
    // 获取模块名称，控制器名称，方法名称

    // 加载公用方法
    include ROOT_PATH . 'includes/function.php';

    if(URL_MODEL == 1){
      //完成URL解析、路由和调度
      include ROOT_PATH . 'includes/core/dispatcher.class.php';
      //URL解析、路由和调度
      new dispatcher();
    }else{
      define("MODULE_NAME", isset($_GET['m']) ? strip_tags(strtolower(trim($_GET['m']))) : DEFAULT_MODULE);
      define("CONTROLLER_NAME", isset($_GET['c']) ? strip_tags(strtolower(trim($_GET['c']))) : "index");
      define("ACTION_NAME", isset($_GET['a']) ? strip_tags(strtolower(trim($_GET['a']))) : "index");
    }

    // 加载核心类
    include ROOT_PATH . 'includes/core/controller.class.php';
    include ROOT_PATH . 'includes/core/model.class.php';

    //开启session
    session_start();
  }

  // 检测开发环境
  private static function setReporting()
  {
     if (APP_DEBUG === true) {
         error_reporting(E_ALL);
         ini_set('display_errors','On');
     } else {
         error_reporting(E_ALL);
         ini_set('display_errors','Off');
         ini_set('log_errors', 'On');
     }
  }

  //自动载入
  private static function autoload() {
      spl_autoload_register('self::load');
  }


  //此处只加载application中的controller和model
  public static function load($classname) {
      if (substr($classname, -10) == 'Controller') {
          //控制器
          $classfile = APP_PATH . MODULE_NAME . '/controllers' . "/{$classname}.class.php";
          if(file_exists($classfile)){
            include $classfile;
          }
      } elseif (substr($classname, -5) == 'Model') {
          //模型
          $classfile = APP_PATH . MODULE_NAME . '/model' . "/{$classname}.class.php";
          if(file_exists($classfile)){
            include $classfile;
          }
      } else {
          //暂略

      }
  }

  //路由分发，即实例化对象调用方法
  //index.php?m=admin&c=goods&a=add--后台的GoodsController中的addAction
  private static function dispatch() {
    $controller_name = CONTROLLER_NAME. "Controller";
    $action_name = ACTION_NAME;
    //实例化对象
    if(class_exists($controller_name)){
      $controller = new $controller_name();
    }else{
      trace('controller does not exist',404);
    }
    //调用方法
    if(method_exists($controller_name,$action_name)){
      $controller->$action_name();
    }else{
      trace('action does not exist',404);
    }
  }
}
