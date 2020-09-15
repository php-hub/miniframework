<?php
namespace core;

/*

$rule = [
    'name'  => 'require',
    'age'   => 'require|number',
    'email' => 'email',
];

$data = [
    'name'  => 'thinkphp',
    'age'   => 10,
    'email' => 'thinkphp@qq.com',
];

$msg = [
    'name.require' => '名称必须',
    'name.max'     => '名称最多不能超过25个字符',
    'age.number'   => '年龄必须是数字',
    'age.between'  => '年龄只能在1-120之间',
    'email.email'        => '邮箱格式错误',
];

$validate = new Validate($rule);
$result   = $validate->check($data);

*/
class Validate{
    private $rule;
    private $msg;
    public  $errorMsg = '';

    public function __construct($rule, $msg = ''){
        $this->rule = $rule;
        $this->msg = $msg;
    }

    // 验证
    public function check($data){
        foreach($data as $k=>$v){
            $this->validate($k,$v);
        }
        if( empty($this->errorMsg) ){
            return true;
        }else{
            return false;
        }
    }

    // 验证方法
    private function validate($key,$value){
        if( isset($this->rule[$key]) ){
            $urle_arr = explode("|", $this->rule[$key] ); // 拆分规则 require|number
            foreach($urle_arr as $validate_rule){
                // 必填
                if($validate_rule === 'require' && empty($value)){
                    $this->setErrorMsg($key,$validate_rule);
                }
                // 数字
                if($validate_rule === 'number' && !is_numeric($value) ){
                    $this->setErrorMsg($key,$validate_rule);
                }
                // 整数
                if($validate_rule === 'integer' && (!is_numeric($value) || strpos($value,".")!==false) ){
                    $this->setErrorMsg($key,$validate_rule);
                }
                // email
                if($validate_rule === 'email' && !preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $value) ){
                    $this->setErrorMsg($key,$validate_rule);
                }
                // 纯字母
                if($validate_rule === 'alpha' && !preg_match("/^[A-Za-z]+$/",$value) ){
                    $this->setErrorMsg($key,$validate_rule);
                }
                // 字母+数字
                if($validate_rule === 'alphaNum' && !preg_match("/^[A-Za-z0-9]+$/",$value) ){
                    $this->setErrorMsg($key,$validate_rule);
                }
            }
        }
    }

    private function setErrorMsg( $key, $validate ){
        if(isset($this->msg[ $key.'.'.$validate ])){
            $this->errorMsg .= '['. $key .']' .$this->msg[$key.'.'.$validate ] . ',';
        }else{
            $this->errorMsg .= '['. $key .']' .$this->defaultErrors($validate) . ',';
        }
    }

    public function getError(){
        if( !empty($this->errorMsg) ){
            return rtrim($this->errorMsg, ",");
        }else{
            return 0;
        }
        
    }

    // 内置错误提示
    private function defaultErrors($k){
        $msg = [
            'require'       => 'Required',
            'number'        => 'Number',
            'integer'       => 'Integer',
            'email'         => 'Email',
            'alpha'         => 'Alpha',
            'alphaNum'      => 'Alpha or Number',
        ];
        return $msg[$k];
    }

}