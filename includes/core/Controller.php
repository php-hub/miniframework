<?php
namespace core;

//核心控制器
class Controller {
  protected $variables = array();

  // 分配变量
  protected function assign($name, $value)
  {
      $this->variables[$name] = $value;
  }

  // 渲染视图
  protected function render($file = '')
  {
      extract($this->variables);
      $theme_path = APP_PATH  .'/'. MODULE_NAME . '/view/';
      if(empty($file)){
        $tpl = $theme_path . CONTROLLER_NAME . "/" . ACTION_NAME . '.php';
      }else{
        $tpl = $theme_path . $file . '.php';
      }

      if(file_exists($tpl)){
        include $tpl;
      }else{
        trace("找不到模板");
      }

  }

  // 载入子模板
  // $path : common/header 实现路径：admin/common/header.php
  protected function load_template_part($path){
    extract($this->variables);
    $theme_path = APP_PATH  . MODULE_NAME .'/view/';
    $tpl = $theme_path . $path . '.php';
    if(file_exists($tpl)){
      include $tpl;
    }else{
      trace("找不到子模板");
    }
  }


  /*写缓存
    $name:缓存名称
    $value: 值
    $expire: 过期时间
    $prefix: 前缀
  */
  protected function set_cache( $name , $value , $expire = null , $prefix = '' ){
    $this->load_library("cache");
    $obj = new Cache( ["temp" => RUNTIME_PATH . 'cache/' , "prefix"=>$prefix] );
    $obj->set($name,$value,$expire);
  }

  // 读缓存
  protected function get_cache( $name , $prefix = '' ){
    $this->load_library("cache");
    $obj = new Cache( ["temp" => RUNTIME_PATH . 'cache/' , "prefix"=>$prefix] );
    return $obj->get($name);
  }

  // 写入日志

  protected function write_log($info){
    $this->load_library("log");
    $obj = new log( ['path' => RUNTIME_PATH . 'log/' ] );
    $obj->save($info);
  }

  

}