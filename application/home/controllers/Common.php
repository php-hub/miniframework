<?php
namespace app\home\controllers;

class Common extends \core\Controller{

  public $key;
  // 构造方法
  protected function __construct(){
    $this->key = md5("123");
  }

  // 返回JSON
  protected function ajaxReturn($data){
    echo json_encode($data);
  }
}