<?php
/**
 * 完成URL解析、路由和调度
 */
class Dispatcher {
    /**
     * URL映射到控制器
    **/
    public function __construct() {
      // 普通模式 http://serverName/index.php?s=/应用/控制器/操作/参数名/参数值...]
      if( isset($_GET["s"]) && key($_GET) === 's' ){
        $params   =  explode('/' , $_GET["s"]);
        define('MODULE_NAME',       isset($params[0])?$params[0]:DEFAULT_MODULE );
        define('CONTROLLER_NAME',   isset($params[1])?$params[1]:DEFAULT_CONTROLLER);
        define('ACTION_NAME',       isset($params[2])?$params[2]:DEFAULT_ACTION);
        // 合并其它URL参数
        if( count($params) >3 ){
          for($i=3; $i<count($params); $i++ ){
            if($i%2 != 0 && isset($params[$i])){
              $_GET[$params[$i]] = $this->trimSuffix(isset( $params[$i+1] )?$params[$i+1]:'');
            }
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

      $path_info  =   $this->trimSuffix($_SERVER['PATH_INFO']); // 去掉后缀

      if (!empty($path_info)){
        // 调用路由方法
        if(URL_ROUTE_ON == true){
          $getRules = $this->route($path_info);
          if($getRules){
            // 匹配成功
            define('MODULE_NAME',      $getRules["module"]);
            define('CONTROLLER_NAME',  $getRules["controller"]);
            define('ACTION_NAME',      $getRules["action"]);
            return;
          }
        }

        // 没有匹配
        $params = explode('/' ,$path_info);
        define('MODULE_NAME',       isset($params[0])?$params[0]:DEFAULT_MODULE );
        define('CONTROLLER_NAME',   isset($params[1])?$params[1]:DEFAULT_CONTROLLER);
        define('ACTION_NAME',       isset($params[2])?$params[2]:DEFAULT_ACTION);

        for($i=3; $i<count($params); $i++ ){
          if($i%2 != 0 && isset($params[$i])){
            $_GET[$params[$i]] = $this->trimSuffix(isset( $params[$i+1] )?$params[$i+1]:'');
          }
        }

        
        
      }else{
        define('MODULE_NAME', DEFAULT_MODULE);
        define('CONTROLLER_NAME', DEFAULT_CONTROLLER);
        define('ACTION_NAME', DEFAULT_ACTION);
      }

      //保证$_REQUEST正常取值
      // $_REQUEST = array_merge($_POST,$_GET);
    }

    // 去掉后缀 (html)
    private function trimSuffix($url){
      return str_replace('.'.URL_HTML_SUFFIX, '', $url); // 去掉后缀
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

    // 调用路由 pahtinfo: demo/12/qing ; rule : ['demo/:id/:name' => 'home/demo/index']
    private function route($pahtinfo){
      $results = false;
      include_once ROOT_PATH . "config/route.php"; // 载入路由规则

      // 拆分pahtinfo
      $pahtinfos = explode("/", trim($pahtinfo,'/') );
      foreach($route_rules as $rule_item){ // $rule_item = ['demo/:id/:name' => 'home/demo/index'];
        foreach($rule_item as $item_key => $item_value ){
          $item_key_child   = explode("/", $item_key);    // 拆分健 demo/:id/:name
          $item_value       = explode("/", $item_value);  // 拆分健 'home/demo/index
          if($item_key_child[0] === $pahtinfos[0]){
            // 模型/控制器/方法 
            $results["module"]      = $this->replaceSpecialChar($item_value[0]);
            $results["controller"]  = $this->replaceSpecialChar($item_value[1]);
            $results["action"]      = $this->replaceSpecialChar($item_value[2]);
            // 获取参数
            for( $c = 1; $c < count($item_key_child); $c++ ){
              $param = explode(":", $item_key_child[$c] );
              if( count($param) > 1 ){
                $_GET[$param[1]] = isset($pahtinfos[$c])?$pahtinfos[$c]:'';
              }
            }
            break; //终止循环
          }
        }
      }
      return $results;
    }

}
