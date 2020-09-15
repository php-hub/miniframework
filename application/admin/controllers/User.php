<?php
namespace app\admin\controllers;
use app\admin\controllers\Admin;

class User extends Admin{
  // 构造方法
  public function __construct(){
    parent::__construct();
  }

  // 登录
  public function login(){

    $username = input("post.username");
    $password = input("post.password");
    $vercode = strtolower(input("post.vercode"));

    // 验证数据
    $validateRule = [
      'username'  => 'require',
      'password'  => 'require',
    ];

    $data = [
      'username'  => input("post.username"),
      'password'  => input("post.password"),
    ];

    $validate = new \core\Validate($validateRule);
    if( !$validate->check($data) ){
      exit($validate->getError());
    }

    if( $vercode !== strtolower($this->session("admin_login_captcha")) ){
      $data = ["vercode"=>$vercode,"session"=>strtolower($this->session("admin_login_captcha"))];
      ajaxReturn(["code"=>0, "status"=>0,"msg"=>"验证码不正确","data"=>$data]);
    }else if($data["username"] == 'admin' && $data["password"] == '123456'){
      $this->session("admin_user",$data["username"]);
      ajaxReturn(["code"=>0, "status"=>1, "msg"=>"successfull","data"=>["access_token"=>md5($data["username"])]]);
    }else{
      ajaxReturn(["code"=>0, "status"=>0,"msg"=>"密码不正确","data"=>'']);
    }
  }

  // 验证码
  public function captcha(){
    $captcha = new \extend\captcha\Captcha(INC_PATH . '/extend/captcha/font/Elephant.ttf');
    $captcha->backgroundcolor = [250,250,250];
    $captcha->doimg();
    $this->session("admin_login_captcha",$captcha->getCode());
  }

  // 退出登录
  public function logout(){
    $this->session("admin_user",null);
    ajaxReturn(["code"=>0, "status"=>1,"msg"=>"成功退出"]);
  }

  // 用户信息
  public function userinfo(){
    $username = $this->session("admin_user");
    if($username){
      ajaxReturn(["code"=>0, "msg"=>"successfull","data"=>[ "username"=>"彭庆" ]]);
    }else{
      ajaxReturn(["code"=>1001, "status"=>0,"msg"=>"登录超时","data"=>'']);
    }
  }

}
