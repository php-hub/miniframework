<?php
/**
 * 完成URL解析、路由和调度
 */
class dispatcher {
    /**
     * URL映射到控制器
     * @access public
     * @return void
     */
    public function __construct() {
        $varModule      =   'm';
        $varController  =   'c';
        $varAction      =   'a';
        if(IS_CLI){ // CLI模式下 index.php module/controller/action/params/...
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
        // 服务器是否支持PATH_INFO
        if(empty($_SERVER['PATH_INFO'])) {
          $_SERVER['PATH_INFO'] = '';
        }

        $depr = URL_PATHINFO_DEPR;
        define('__INFO__',trim($_SERVER['PATH_INFO'],'/'));
        // URL后缀
        define('__EXT__', strtolower(pathinfo($_SERVER['PATH_INFO'] , PATHINFO_EXTENSION )));
        $_SERVER['PATH_INFO'] = __INFO__;

        // 获取模块名
        if (__INFO__){
            $paths      =   explode($depr,__INFO__,2);
            $first_path     =   preg_replace('/\.' . __EXT__ . '$/i', '',$paths[0]);
            // 隐藏默认模块名
            $allow_modules =   explode(',',MODULE_ALLOW_LIST);
            if(!in_array($first_path,$allow_modules)){
              $module = DEFAULT_MODULE;
            }else{
              $module = $first_path;
              $_SERVER['PATH_INFO']   =   isset($paths[1])?$paths[1]:''; // 删除模块名，返回剩余的字符串
            }
            $_GET[$varModule]  =  $module;
        }

        // 获取模块名称
        define('MODULE_NAME', $this->getModule($varModule));

        // 获得控制器名称和方法名
        if($_SERVER['PATH_INFO'] != ''){
            // 去除URL后缀
            $_SERVER['PATH_INFO'] = preg_replace( URL_HTML_SUFFIX ? '/\.('.trim(URL_HTML_SUFFIX,'.').')$/i' : '/\.'.__EXT__.'$/i', '', $_SERVER['PATH_INFO']);
            $paths  =   explode($depr,trim($_SERVER['PATH_INFO'],$depr));
            // 获取控制器
            $_GET[$varController]   =   array_shift($paths); // 删除数组中的第一个元素（red），并返回被删除元素的值：
            // 获取操作
            $_GET[$varAction]  =   array_shift($paths);
            // 解析剩余的URL参数
            $var  =  array();
            preg_replace_callback('/(\w+)\/([^\/]+)/', function($match) use(&$var){$var[$match[1]]=strip_tags($match[2]);}, implode('/',$paths));
            $_GET   =  array_merge($var,$_GET);

            // 调用路由方法
            $routeUrl = $this->route($_SERVER['PATH_INFO']);
            if(!empty($routeUrl) && MODULE_NAME != 'admin'){
              define('CONTROLLER_NAME',  $routeUrl["controller"]);
              define('ACTION_NAME',      $routeUrl["action"]);
              //保证$_REQUEST正常取值
              $_REQUEST = array_merge($_POST,$_GET);
              return;
            }

        }

        // 获取控制器和操作名
        define('CONTROLLER_NAME',   $this->getController($varController));
        define('ACTION_NAME',       $this->getAction($varAction));


        //echo "<br/>m:".MODULE_NAME;
        //echo "<br/>c:".CONTROLLER_NAME;
        //echo "<br/>a:".ACTION_NAME;

        //保证$_REQUEST正常取值
        $_REQUEST = array_merge($_POST,$_GET);
    }

    /**
     * 获得实际的模块名称
     * @access private
     * @return string
     */
    private function getModule($var) {
      $module  = (!empty($_GET[$var])?$_GET[$var]:DEFAULT_MODULE);
      unset($_GET[$var]);
      return $this->replaceSpecialChar(strtolower($module));
    }

    /**
     * 获得实际的控制器名称
     * @access private
     * @return string
     */
    private function getController($var) {
      $controller = (!empty($_GET[$var])? $_GET[$var]:'index');
      unset($_GET[$var]);
      return $this->replaceSpecialChar(strtolower($controller));
    }

    /**
     * 获得实际的操作名称
     * @access private
     * @return string
     */
    private function getAction($var) {
      $action   = !empty($_POST[$var]) ? $_POST[$var] : (!empty($_GET[$var])?$_GET[$var]:'index');
      unset($_POST[$var],$_GET[$var]);
      $action = strtolower($action);
      return $this->replaceSpecialChar($action);
    }

    // 过滤特殊字符
    private function replaceSpecialChar($strParam){
        $regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\s|\.|\/|\;|\'|\`|\=|\\\|\|/";
        $result = preg_replace( $regex , "" , strip_tags($strParam) );
        if (!preg_match('/^[A-Za-z](\w|\.)*$/', $result)) {
          trace('非法操作',404);
        }
        return $result;
    }

    // 调用路由
    private function route($pahtinfo){
      $results = '';
      include ROOT_PATH . "route.php"; // 载入路由规则
      //$results["controller"] = 'index';
      //$results["action"] = 'index';
      // 拆分Pathinfo
      $pahtinfos = explode("/", $pahtinfo);

      if($pahtinfos > 0){
        foreach($route_rules as $k=>$v){
          //echo 'key:' . key($v) ;
          //echo 'value:' . $v[key($v)];
          // 拆分路由KEY category/:id
          $rule_keys = explode("/", key($v));

          if(isset($rule_keys[0]) && $rule_keys[0] === $pahtinfos[0]){
            $rule_values = explode( "/", $v[key($v)] );
            $results["controller"] = $rule_values[0];
            $results["action"] = $rule_values[1];
            // 合并参数到GET
            if(isset($pahtinfos[1]) && isset($rule_keys[1])){
              $_GET[ltrim($rule_keys[1], ':')] = $pahtinfos[1];
            }

            break; //终止循环
          }
        }
      }
      return $results;
    }

}
