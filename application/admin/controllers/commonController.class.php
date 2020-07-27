<?php
class commonController extends controller{
  // 属性

  public $userinfo = '';

  // 构造方法
  public function __construct(){

    // 模块，方法名
    // CONTROLLER_NAME
    // ACTION_NAME

    // 后台公用函数
    include APP_PATH . '/admin/function.php';
    if( empty( $_SESSION['user_name'] ) ) {
      // 未登录
      header("HTTP/1.1 301 Moved Permanently");
      redirect( url("admin/login/index") );
    }else{
      // 登录成功
       $this->assign("user_name", $_SESSION['user_name']);
       $this->userinfo = $_SESSION['user_name'];
    }

  }

  // 寫入日志
  public function log($type, $content){
    $data["type"]     = $type;
    $data["content"]  = $content;
    $data["crt_time"]  = time();
    $data["crt_ip"]  = get_ip();
    
    $logModel = new logModel;
    $ret = $logModel->save($data);
  }

}
