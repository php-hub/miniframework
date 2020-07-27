<?php
class loginController extends controller{
  // 登录页
  public function index(){
    // 生成密碼
    /*
    $data["user"]     = "sasa";
    $data["password"] = md5(ADMIN_USER_KEY."123456");
    echo json_encode($data);
    */
    $this->render();
  }

  // 会员登录
  public function check(){
    $captcha = input("post.captcha");
    $user_name = input("post.username");
    $user_password = input("post.password");
    // 检查验证码
    if( strtolower($_SESSION['admin_login_captcha']) !==  strtolower( $captcha ) ){
      ajax_return(['status'=>0,"msg"=>'驗證碼不正確']);
    }

    if( empty($captcha ) || empty($user_name ) || empty($user_password )){
      ajax_return(['status'=>0,"msg"=>'非法登入']);
    }else{
      $config_model = new configModel;
      $ret = $config_model->get_config("user");
      if($ret){
        $data = json_decode($ret["value"], true);
        if($data["user"] === $user_name && $data["password"] === md5(ADMIN_USER_KEY.$user_password) ){
          // 登录成功
          $_SESSION['user_name'] = $data["user"];
          ajax_return(['status'=>1,"msg"=>'登入成功']);
        }
      }
      ajax_return(['status'=>0,"msg"=>'帳號或密碼不正確']);
    }
    
  }

  // 退出登录
  public function logout(){
    $_SESSION['user_name'] = '';
    redirect( url("admin/login/index") );
  }

  public function captcha(){
    $this->load_extend("captcha");
    $_vc = new ValidateCode(INC_PATH . '/extend/');  //实例化一个对象
    $_vc->backgroundcolor = [242,241,241];
    $_vc->doimg();
    $_SESSION['admin_login_captcha'] = $_vc->getCode(); //验证码保存到SESSION中
  }

}
