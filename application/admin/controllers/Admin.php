<?php
namespace app\admin\controllers;
use \core\Controller;

class Admin extends Controller{

  public $key;
  // 构造方法
  protected function __construct(){
    $this->key = md5("123");
  }
}