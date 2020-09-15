<?php
// 路由规则
// 1.每个参数中以“:”开头的参数都表示动态参数，并且会自动对应一个GET参数，例如:id表示该处匹配到的参数可以使用$_GET['id']
// 2.路由规则路径暂时只支持到一级。如：home/about 是错误的
// 3.规则值：模型/控制器/方法
$route_rules = [
  ['demo/:name/:id' => 'home/demo/index'],
  ['upload' => 'home/index/upload'],
  ['myhome' => 'home/index/index'],
];
