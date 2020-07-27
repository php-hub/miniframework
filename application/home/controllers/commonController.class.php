<?php
class commonController extends controller{
  // 构造方法
  public function __construct(){
    
  }

  // 返回JSON
  public function ajaxReturn($data){
    echo json_encode($data);
  }
}
