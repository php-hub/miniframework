<?php
namespace app\home\controllers;

import('@.controllers.Common');
use app\home\controllers\Common as mycommon;
class Index extends mycommon{
  
  // 构造方法
  public function __construct(){
    parent::__construct();
  }

  // 主页
  public function index(){
    echo $this->key ."<br/>";
    $this->assign("token",'');
    echo url("demo#step",["id"=>10021,"name"=>"hinson"]);
    //echo $this->ajaxReturn(["code"=>0,"msg"=>"这里调用了common函数"]);
    $this->render();
  }

  // 调用跨模块方法
  public function use_common_module(){
    $obj = new \app\common\controllers\Table;
    echo $obj->index();
  }

  // 上传
  public function upload(){
    $this->render();
    if( isset($_FILES["image"] )){
      $config = ["savePath"=>CONTENT."assets/upload/"];
      $upload = new \extend\Upload($config);
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

  // 插入数据到数据库
  public function add(){
    echo "数据库操作演示：<br/>";
    $userModel = new \app\home\model\User;
    if( $userModel->add("彭庆".time()) ){
      echo "数据插入成功";
    }else{
      echo "数据插入失败";
    }
  }

  // 读数据列表
  public function getlist(){
    echo "数据库操作演示：<br/>";
    $userModel = new \app\home\model\User;
    $res = $userModel->getList();
    if( $res ){
      echo "数据查询成功<hr/>";
      var_dump($res);
    }else{
      echo "数据查询失败";
    }
  }
  // 读单条数据
  public function getone(){
    echo "数据库操作演示：<br/>";
    $userModel = new \app\home\model\User;
    $res = $userModel->getOne(1);
    if( $res ){
      echo "数据查询成功<hr/>";
      var_dump($res);
    }else{
      echo "数据查询失败";
    }
  }  

  // 删除单条数据
  public function delete(){
    echo "数据库操作演示：<br/>";
    $userModel = new \app\home\model\User;
    $res = $userModel->delete(1);
    if( $res ){
      echo "数据删除成功<hr/>";
    }else{
      echo "数据删除失败";
    }
  }  

  // 缓存
  public function testcache(){
    $action = input("get.action");
    if( $action == "set" ){
      if( $this->setCache("textCache","这里是缓存。10秒到期",10) ){
        echo '缓存保存成功';
      }else{
        echo '缓存保存失败';
      }
    }else if( $action == "get" ){
      $res = $this->getCache("textCache");
      if( $res ){
        echo $res;
      }else{
        echo '缓存已过期';
      }
    }
  }

  // 写入日志
  public function testlog(){
    if( $this->log("测试写入日志") ){
      echo '日志写入成功';
    }else{
      echo '日志写入失败';
    }
  }

  // 验证码
  public function captcha(){
    $this->render();
  }

  // 生成验证码
  public function get_gaptcha_img(){
    $captcha = new \extend\captcha\Captcha(INC_PATH . '/extend/captcha/font/Elephant.ttf');
    $captcha->backgroundcolor = [250,250,250];
    $captcha->doimg();
    $_SESSION['admin_login_captcha'] = $captcha->getCode();
  }

}
