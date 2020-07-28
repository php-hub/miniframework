<?php
/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型

 * @param mixed $filter 参数过滤方法
 * @return mixed
 */
 // strip_tags() 函数剥去字符串中的 HTML、XML 以及 PHP 的标签。
 // htmlspecialchars_decode()  [ htmlspecialchars 相反]

function input($name, $filter = 'htmlspecialchars') {
    if(strpos($name,'.')) { // 指定参数来源
        list($method,$name) =   explode('.',$name,2);
    }else{ // 默认为自动判断
        $method =   'param';
    }
    switch(strtolower($method)) {
        case 'get'     :   $input =& $_GET;break;
        case 'post'    :   $input =& $_POST;break;
        case 'put'     :   parse_str(file_get_contents('php://input'), $input);break;
        case 'param'   :
            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input  =  $_POST;
                    break;
                case 'PUT':
                    parse_str(file_get_contents('php://input'), $input);
                    break;
                default:
                    $input  =  $_GET;
            }
            break;
        case 'request' :   $input =& $_REQUEST;   break;
        case 'session' :   $input =& $_SESSION;   break;
        case 'cookie'  :   $input =& $_COOKIE;    break;
        case 'server'  :   $input =& $_SERVER;    break;
        case 'globals' :   $input =& $GLOBALS;    break;
        default:
            return NULL;
    }
    if(empty($name)) { // 获取全部变量
        $data       =   $input;
        array_walk_recursive($data,'filter_exp');
        $filters    =   isset($filter)?$filter:'';
        if($filters) {
            $filters    =   explode(',',$filters);
            foreach($filters as $filter){
                $data   =   array_map_recursive($filter,$data); // 参数过滤
            }
        }
    }elseif(isset($input[$name])) { // 取值操作
        $data       =   $input[$name];
        is_array($data) && array_walk_recursive($data,'filter_exp');
        $filters    =   isset($filter)?$filter:'';
        if($filters) {
            $filters    =   explode(',',$filters);
            foreach($filters as $filter){
                if(function_exists($filter)) {
                    $data   =   is_array($data)?array_map_recursive($filter,$data):$filter($data); // 参数过滤
                }else{
                    $data   =   filter_var($data,is_int($filter)?$filter:filter_id($filter));
                    if(false === $data) {
                        return  NULL;
                    }
                }
            }
        }
    }else{ // 变量默认值
        $data  = NULL;
    }
    return $data;
}

// 过滤表单中的表达式
function filter_exp(&$value){
    if (in_array(strtolower($value),array('exp','or'))){
        $value .= ' ';
    }
}
function array_map_recursive($filter, $data) {
   $result = array();
   foreach ($data as $key => $val) {
       $result[$key] = is_array($val)
           ? array_map_recursive($filter, $val)
           : call_user_func($filter, $val);
   }
   return $result;
}

//是否是AJAX提交
function is_ajax(){
  if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    return true;
  }else{
    return false;
  }
}

//是否是GET提交
function is_get(){
  return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
}

//是否是POST提交
function is_post(){
  return ($_SERVER['REQUEST_METHOD'] == 'POST' &&  (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? true : false;
}



/**
* 浏览器友好的变量输出
* @param mixed $var 变量
* @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
* @param string $label 标签 默认为空
* @param boolean $strict 是否严谨 默认为true
* @return void|string
*/
function dump($var, $echo=true, $label=null, $strict=true) {
   $label = ($label === null) ? '' : rtrim($label) . ' ';
   if (!$strict) {
       if (ini_get('html_errors')) {
           $output = print_r($var, true);
           $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
       } else {
           $output = $label . print_r($var, true);
       }
   } else {
       ob_start();
       var_dump($var);
       $output = ob_get_clean();
       if (!extension_loaded('xdebug')) {
           $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
           $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
       }
   }
   if ($echo) {
       echo($output);
       return null;
   }else
       return $output;
}


// 404页面
// $status 200:页面跟踪, 404:页面不存在
function trace($msg , $status = 200){
 if($status == 404){
   header("HTTP/1.1 404 Not Found");
   include_once ROOT_PATH . 'includes/tpl/404.php';
 }else{
   include_once ROOT_PATH . 'includes/tpl/page_trace.php';
 }
 die();
}

// return json
function ajax_return($data){
  echo json_encode($data);
  die();
}


// 无限分类 数据转为树型状的数组
function listTree($data, $idKey = 'id', $pId = 0, $subname = 'list'){
  $tree = '';
  foreach($data as $k => $v)
  {
    if($v['pid'] == $pId)
    {        //父亲找到儿子
      if(isset($v[$idKey])){
        $v[$subname] = listTree($data, $idKey, $v[$idKey], $subname);
      }

     $tree[] = $v;
    }
  }
  return $tree;
}

// 无限分类，下拉框格式
function treeSelect($list, $idKey = "id", $pid=0, $level=0, $html='&nbsp;&nbsp;&nbsp;&nbsp;'){
	static $tree = array();
	foreach($list as $v){
		if($v['pid'] == $pid){
			$v['level'] = $level;
			$v['html'] = str_repeat($html,$level);
			$tree[] = $v;
      if(isset($v[$idKey])){
        treeSelect($list, $idKey, $v[$idKey], $level+1, $html);
      }

		}
	}
  return $tree;
	unset($tree);
}


/**
* 获取客户端IP地址
* @access public
* @param  integer   $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
* @param  boolean   $adv 是否进行高级模式获取（有可能被伪装）
* @return mixed
*/
function get_ip($type = 0, $adv = true){
    $type      = $type ? 1 : 0;
    static $ip = null;
  
    if (null !== $ip) {
        return $ip[$type];
    }
  
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim(current($arr));
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
  
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];
  
    return $ip[$type];
  }

// 页面跳转
function redirect($url){
    header("Location: ". $url);
    exit();
}

// 载入文件
function import($path){
    $file = str_replace(".","/",$path);
    $paths = explode('/' , $file);
    if($paths[0] === '@'){
        $file = str_replace("@",MODULE_NAME,$file);
    }
    $file = APP_PATH . $file.'.php';
    if (file_exists($file)) {
        if (is_file($file)) {
            include_once $file;
        }
    }else{
        trace('找不到文件!');
    }
    
}