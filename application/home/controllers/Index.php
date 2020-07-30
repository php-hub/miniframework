<?php
namespace app\home\controllers;

import('@.controllers.Common');
use app\home\controllers\Common as mycommon;
class Index extends mycommon{
  
  // 构造方法
  public function __construct(){
    //parent::__construct();
  }

  // 主页
  public function index(){
    $this->assign("token",'');
    echo url("demo#step",["id"=>10021,"name"=>"hinson"]);
    //echo $this->ajaxReturn(["code"=>0,"msg"=>"这里调用了common函数"]);
    $this->render();
  }

  // 上传
  public function upload(){
    $this->render();
    if( isset($_FILES["image"] )){
      $config = ["savePath"=>CONTENT."assets/upload/"];
      $upload = new \library\Upload($config);
      $res = $upload->upload("image");
      if($res){
        echo "上传成功:<br/>";
        echo $upload->uploadFileInfo;
      }else{
        echo "上传失败:<br/>";
        echo $upload->errorMsg;
      }
    }
  }

  public function demo(){
    echo "DEMO页：<br/>";
    $obj = new \app\home\model\Test;
    $obj->hello();
  }
  
  public function mycache(){
    // 缓存
    $cache = new \library\Cache(["temp"=>RUNTIME_PATH."cache/"]);
    if( !$cache->get("test1") ){
      echo '写入成功：';
      $cache->set("test1",["code"=>1,"msg"=>"我是数据"],15);
    }else{
      echo '读取成功：<br/>';
      dump( $cache->get("test1"));
    }
    // 日志
    $log = new \library\Log(["path"=>RUNTIME_PATH."log/"]);
    $log->save("系统出错");
  }

  // 验证码
  public function captcha(){
    $this->render();
  }

  public function get_gaptcha_img(){
    $captcha = new \extend\captcha\Captcha(INC_PATH . '/extend/captcha/font/Elephant.ttf');
    $captcha->backgroundcolor = [250,250,250];
    $captcha->doimg();
    $_SESSION['admin_login_captcha'] = $captcha->getCode();
  }

}
