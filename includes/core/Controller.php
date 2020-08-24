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
        $tpl = $theme_path . ucfirst(CONTROLLER_NAME) . "/" . ACTION_NAME . '.php';
      }else{
        $tpl = $theme_path . $file . '.php';
      }

      if(file_exists($tpl)){
        include_once $tpl;
      }else{
        error("找不到模板");
      }

  }

  // 包含模板
  // $path : common/header 实现路径：admin/common/header.php
  protected function include_template($path){
    extract($this->variables);
    $theme_path = APP_PATH  . MODULE_NAME .'/view/';
    $tpl = $theme_path . $path . '.php';
    if(file_exists($tpl)){
      include $tpl;
    }else{
      error("找不到子模板");
    }
  }


  /*写缓存
    $name:缓存名称
    $value: 值
    $expire: 过期时间
    $prefix: 前缀
  */
  protected function cache($name , $value = '' , $expire = null){
    $cache = new \core\Cache(["temp"=>RUNTIME_PATH."cache/"]);
    if( empty($name) ){
      return false;
    }

    if( $value === null ){
      return $cache->rm($name); // 删除缓存
    }else if( $value != ''){
      return $cache->set($name,$value,$expire); // 设置缓存
    }else{
      return $cache->get($name); // 读缓存
    }
    
  }


  // 写入日志

  protected function log($info){
    $obj = new \core\Log( ['path' => RUNTIME_PATH . 'log/' ] );
    return $obj->save($info);
  }

  
/**
 * session管理函数
 * @param string|array $name session名称 如果为数组则表示进行session设置
 * @param mixed $value session值
 * @return mixed
 */
protected function session($name='',$value='') {
  $prefix   =  SESSION_PREFIX;

  if('' === $value){ 
      if(''===$name){
          // 获取全部的session
          return $prefix ? $_SESSION[$prefix] : $_SESSION;
      }elseif(is_null($name)){ // 清空session
          if($prefix) {
              unset($_SESSION[$prefix]);
          }else{
              $_SESSION = array();
          }
      }elseif($prefix){ // 获取session
          if(strpos($name,'.')){
              list($name1,$name2) =   explode('.',$name);
              return isset($_SESSION[$prefix][$name1][$name2])?$_SESSION[$prefix][$name1][$name2]:null;  
          }else{
              return isset($_SESSION[$prefix][$name])?$_SESSION[$prefix][$name]:null;                
          }            
      }else{
          if(strpos($name,'.')){
              list($name1,$name2) =   explode('.',$name);
              return isset($_SESSION[$name1][$name2])?$_SESSION[$name1][$name2]:null;  
          }else{
              return isset($_SESSION[$name])?$_SESSION[$name]:null;
          }            
      }
  }elseif(is_null($value)){ // 删除session
      if(strpos($name,'.')){
          list($name1,$name2) =   explode('.',$name);
          if($prefix){
              unset($_SESSION[$prefix][$name1][$name2]);
          }else{
              unset($_SESSION[$name1][$name2]);
          }
      }else{
          if($prefix){
              unset($_SESSION[$prefix][$name]);
          }else{
              unset($_SESSION[$name]);
          }
      }
  }else{ // 设置session
    if(strpos($name,'.')){
      list($name1,$name2) =   explode('.',$name);
      if($prefix){
        $_SESSION[$prefix][$name1][$name2]   =  $value;
      }else{
        $_SESSION[$name1][$name2]  =  $value;
      }
    }else{
      if($prefix){
        $_SESSION[$prefix][$name]   =  $value;
      }else{
        $_SESSION[$name]  =  $value;
      }
    }
  }
  return null;
}

/**
* Cookie 设置、获取、删除
* @param string $name cookie名称
* @param mixed $value cookie值
* @param mixed $option cookie参数
* @return mixed
*/
protected function cookie($name='', $value='', $option=null) {
  // 默认设置
  $config = array(
      'prefix'    =>  COOKIE_PREFIX, // cookie 名称前缀
      'expire'    =>  COOKIE_EXPIRE, // cookie 保存时间
      'path'      =>  COOKIE_PATH, // cookie 保存路径
      'domain'    =>  '', // cookie 有效域名
      'secure'    =>  false, //  cookie 启用安全传输
      'httponly'  =>  '', // httponly设置
  );
  // 参数设置(会覆盖黙认设置)
  if (!is_null($option)) {
      if (is_numeric($option))
          $option = array('expire' => $option);
      elseif (is_string($option))
          parse_str($option, $option);
      $config = array_merge($config, array_change_key_case($option));
  }
  if(!empty($config['httponly'])){
      ini_set("session.cookie_httponly", 1);
  }
  // 清除指定前缀的所有cookie
  if (is_null($name)) {
      if (empty($_COOKIE))
          return null;
      // 要删除的cookie前缀，不指定则删除config设置的指定前缀
      $prefix = empty($value) ? $config['prefix'] : $value;
      if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
          foreach ($_COOKIE as $key => $val) {
              if (0 === stripos($key, $prefix)) {
                  setcookie($key, '', time() - 3600, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
                  unset($_COOKIE[$key]);
              }
          }
      }
      return null;
  }elseif('' === $name){
      // 获取全部的cookie
      return $_COOKIE;
  }
  $name = $config['prefix'] . str_replace('.', '_', $name);
  if ('' === $value) {
      if(isset($_COOKIE[$name])){
          $value =    $_COOKIE[$name];
          if(0===strpos($value,'think:')){
              $value  =   substr($value,6);
              return array_map('urldecode',json_decode(MAGIC_QUOTES_GPC?stripslashes($value):$value,true));
          }else{
              return $value;
          }
      }else{
          return null;
      }
  } else {
      if (is_null($value)) {
          setcookie($name, '', time() - 3600, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
          unset($_COOKIE[$name]); // 删除指定cookie
      } else {
          // 设置cookie
          if(is_array($value)){
              $value  = 'think:'.json_encode(array_map('urlencode',$value));
          }
          $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
          setcookie($name, $value, $expire, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
          $_COOKIE[$name] = $value;
      }
  }
  return null;
}

  

}
