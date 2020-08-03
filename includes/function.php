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
    $file = str_replace("@",MODULE_NAME,$file);
    $file = APP_PATH . $file.'.php';
    if (file_exists($file)) {
        if (is_file($file)) {
            include_once $file;
        }
    }else{
        trace('找不到文件!');
    }
    
}


/**
* URL组装 支持不同URL模式
* @param string $url URL表达式，格式：'[模块/控制器/操作#锚点@域名]?参数1=值1&参数2=值2...'
* @param string|array $vars 传入的参数，支持数组和字符串
* @param string $suffix 伪静态后缀，默认为true表示获取配置值
* @return string
*/
function url($url='',$vars='',$suffix = true) {
    // 解析URL
    $info   =  parse_url($url);
    if(isset($info['fragment'])) { // 解析锚点
        $anchor =   $info['fragment'];
        if(false !== strpos($anchor,'?')) { // 解析参数
            list($anchor,$info['query']) = explode('?',$anchor,2);
        }
    }
 
    // 解析参数
    if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
        parse_str($vars,$vars);
    }elseif(!is_array($vars)){
        $vars = array();
    }
    if(isset($info['query'])) { // 解析地址里面参数 合并到vars
        parse_str($info['query'],$params);
        $vars = array_merge($params,$vars);
    }

 
    if(URL_MODEL == 0) { // 普通模式URL转换
        $url  =  "index.php?s=" . strtolower(trim($info["path"],"/"));
        if( !empty($vars) ) {
            $vars_str = '';
            foreach($vars as $k=>$v){
                $vars_str .= "/".$k.'='.$v;
            }
            $url .= $vars_str;
        }
    }else{ // 静态URL模式
        $url  =  "/" . strtolower(trim($info["path"],"/"));
        if(!empty($vars)) {
            $vars_str = '';
            foreach($vars as $k=>$v){
                $vars_str .= $k.'='.$v.'&';
            }
            $url .= '.' . URL_HTML_SUFFIX .'?'.rtrim($vars_str,"&");
        }
    }
    if(isset($anchor)){
        $url  .= '#'.$anchor;
    }
    return $url;
 }

// curl

function curlGet($url) {
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
}

function curlPost($url,$param,$post_file=false){
    $oCurl = curl_init();
    if(stripos($url,"https://")!==FALSE){
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
    }
    if (PHP_VERSION_ID >= 50500 && class_exists('\CURLFile')) {
        $is_curlFile = true;
    } else {
        $is_curlFile = false;
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt($oCurl, CURLOPT_SAFE_UPLOAD, false);
        }
    }
    if (is_string($param)) {
        $strPOST = $param;
    }elseif($post_file) {
        if($is_curlFile) {
            foreach ($param as $key => $val) {
                if (substr($val, 0, 1) == '@') {
                    $param[$key] = new \CURLFile(realpath(substr($val,1)));
                }
            }
        }
        $strPOST = $param;
    } else {
        $aPOST = array();
        foreach($param as $key=>$val){
            $aPOST[] = $key."=".urlencode($val);
        }
        $strPOST =  join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($oCurl, CURLOPT_POST,true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if(intval($aStatus["http_code"])==200){
        return $sContent;
    }else{
        return false;
    }
}
