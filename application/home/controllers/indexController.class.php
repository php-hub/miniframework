<?php
class indexController extends commonController{
  // 属性
  private $model_options;
  // 构造方法
  public function __construct(){
    parent::__construct();
  }


  // 主页
  public function index(){
    $this->assign("token",'');
    $this->render();
  }

}
