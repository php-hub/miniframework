<?php
namespace app\home\controllers;

use app\home\controllers\Common as commonControllers;
use app\common\controllers\Table;
use app\home\model\User as userModel;

class Index extends commonControllers{
  
  // 构造方法
  public function __construct(){
    parent::__construct();
  }

  // 主页
  public function index(){
    echo $this->orderInfo["subject"] ."<br/>";
    $this->assign("token",'');
    echo url("demo#step",["id"=>10021,"name"=>"hinson"]);
    //echo $this->ajaxReturn(["code"=>0,"msg"=>"这里调用了common函数"]);
    echo "<br/>get id:". input("get.id");
    $this->render();
  }

  // 验证数据
  public function validate(){
    $rule = [
      'name'  => 'require',
      'age'   => 'require|number|integer',
      'email' => 'require|email',
      'today' => 'alphaNum',
    ];
    
    $data = [
      'name'  => 'hinson',
      'age'   => "",
      'email' => 'abc@abc.com',
      'today' => 'ttAee3243tt',
    ];

    $msg = [
      'name.require' => '名称必须',
      'name.max'     => '名称最多不能超过25个字符',
      'age.require'   => '年龄必须',
      'age.number'  => '年龄必须数字',
      'age.integer'  => '必须整数',
      'email.require'        => '邮箱不能为空',
      'email.email'        => '邮箱格式错误',
      'today.alphaNum' => '必须为线字母',
    ];

    $obj = new \core\Validate($rule);
    dump( $obj->check($data) );
    dump( $obj->getError() );

  }

  // 调用跨模块方法
  public function use_common_module(){
    $obj = new Table;
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
    $userModel = new userModel;
    if( $userModel->add("彭庆".time()) ){
      echo "数据插入成功";
    }else{
      echo "数据插入失败";
    }
  }

  // 读数据列表
  public function getlist(){
    echo "数据库操作演示：<br/>";
    $userModel = new userModel;
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
    $userModel = new userModel;
    $res = $userModel->getOne(2);
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
    $userModel = new userModel;
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
      if( $this->cache("textCache","这里是缓存。10秒到期",10) ){
        echo '缓存保存成功';
      }else{
        echo '缓存保存失败';
      }
    }else if( $action == "get" ){
      $res = $this->cache("textCache");
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

  // cookie
  public function test_cookie(){
    echo "<pre>set cookie:</pre>";
    //$this->cookie("name","pengqing",20);
    echo "<pre>get cookie:</pre>";
    echo $this->cookie("name");

  }

  // session
  public function test_session(){
    echo "<pre>set session:</pre>";
    //$this->session("name","session:pengqing");

    echo "<pre>get session:</pre>";
    echo $this->session("name");

  }
}
