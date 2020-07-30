<?php
/**
 * 完成URL解析、路由和调度
 */
class Dispatcher {
    /**
     * URL映射到控制器
    **/
    public function __construct() {
      // 普通模式 http://serverName/index.php?s=/应用/控制器/操作/[参数名=参数值...]
      if( isset($_GET["s"]) && key($_GET) === 's' ){
        $params   =  explode('/' , $_GET["s"]);
        define('MODULE_NAME',       isset($params[0])?$params[0]:DEFAULT_MODULE );
        define('CONTROLLER_NAME',   isset($params[1])?$params[1]:DEFAULT_CONTROLLER);
        define('ACTION_NAME',       isset($params[2])?$params[2]:DEFAULT_ACTION);
        if( count($params) >3 ){
          for($i=3; $i<count($params); $i++ ){
            $gets_data = explode('=' , $params[$i]);
            $_GET[$gets_data[0]]   =  $gets_data[1];
          }
        }
        return;
      }


      // PATH_INFO模式
      if(IS_CLI){ // CLI模式下
          $_SERVER['PATH_INFO'] = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
      }
      // 分析PATHINFO信息
      if(!isset($_SERVER['PATH_INFO'])) {
          $types   =  explode(',' , URL_PATHINFO_FETCH);
          foreach ($types as $type){
              if(0===strpos($type,':')) {// 支持函数判断
                  $_SERVER['PATH_INFO'] =   call_user_func(substr($type,1));
                  break;
              }elseif(!empty($_SERVER[$type])) {
                  $_SERVER['PATH_INFO'] = (0 === strpos($_SERVER[$type],$_SERVER['SCRIPT_NAME']))?
                      substr($_SERVER[$type], strlen($_SERVER['SCRIPT_NAME']))   :  $_SERVER[$type];
                  break;
              }
          }
      }

      $_SERVER['PATH_INFO'] = empty($_SERVER['PATH_INFO'])?'':$_SERVER['PATH_INFO'];
      $_SERVER['PATH_INFO'] = trim($_SERVER['PATH_INFO'],'/');

      // URL后缀 (html)
      $url_suffix = strtolower(pathinfo($_SERVER['PATH_INFO'] , PATHINFO_EXTENSION ));
      $path_info  =   preg_replace('/\.' . $url_suffix . '$/i', '', $_SERVER['PATH_INFO']); // 去掉后缀
      // 获取模块名
      if (!empty($path_info)){
        // 调用路由方法
        $getRules = $this->route($path_info);
        if(!empty($getRules)){
          define('MODULE_NAME',  $getRules["module"]);
          define('CONTROLLER_NAME',  $getRules["controller"]);
          define('ACTION_NAME',      $getRules["action"]);
        }else{
          $params = explode('/' ,$path_info);
          define('MODULE_NAME',       isset($params[0])?$params[0]:DEFAULT_MODULE );
          define('CONTROLLER_NAME',   isset($params[1])?$params[1]:DEFAULT_CONTROLLER);
          define('ACTION_NAME',       isset($params[2])?$params[2]:DEFAULT_ACTION);
        }
        
      }else{
        define('MODULE_NAME', DEFAULT_MODULE);
        define('CONTROLLER_NAME', DEFAULT_CONTROLLER);
        define('ACTION_NAME', DEFAULT_ACTION);
      }

      //保证$_REQUEST正常取值
      $_REQUEST = array_merge($_POST,$_GET);
    }

    // 过滤特殊字符
    private function replaceSpecialChar($strParam){
        $regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\s|\.|\/|\;|\'|\`|\=|\\\|\|/";
        $result = preg_replace( $regex , "" , strip_tags($strParam) );
        if (!preg_match('/^[A-Za-z](\w|\.)*$/', $result)) {
          die('非法操作');
        }
        return $result;
    }

    // 调用路由
    private function route($pahtinfo){
      $results = false;
      include ROOT_PATH . "route.php"; // 载入路由规则
      foreach($route_rules as $k=>$v){
        if(key($v) === $pahtinfo){
          $values = explode("/", $v[key($v)]);
          $results["module"]      = $this->replaceSpecialChar($values[0]);
          $results["controller"]  = $this->replaceSpecialChar($values[1]);
          $results["action"]      = $this->replaceSpecialChar($values[2]);
          break; //终止循环
        }
      }
      return $results;
    }

}
