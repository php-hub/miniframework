<?php
// 后台公共函数
/*
$total 总记录
$limit 每页显示多少条记录
$page 当前页面
$url 链接
*/

function paginate($total, $limit, $page = 1, $url = ''){
  // 判断URL中是否带有参数
  $has_parameter = strpos($url, '?');
  if($has_parameter === false){
    $url = $url . '?';
  }else{
    $url = $url . '&';
  }

  $page = ($page > 0)?$page:1;
  // 计算总页数
  $page_count = ceil($total / $limit);
  // 当前页相伶数，如:当前页为5， ...3,4,5,6,7...
  if($page == $page_count || $page == 1){
    // 最后一页
    $adjacents = 4;
  }else{
    $adjacents = 2;
  }


  $tpl = '<div class="row">';
  $tpl .= '<div class="col-sm-12 col-md-5 text-left">';
  $tpl .= '<div class="pagination-data">共'. $total .'条记录 ( 第'. $page . '/' . $page_count .'页 )</div>';
  $tpl .= '</div>';
  $tpl .= '<div class="col-sm-12 col-md-7 text-right">';
  $tpl .= '<ul class="pagination">';
  // 上一页
  if($page > 1 && $page_count > 1 ){
    $tpl .= '<li class=page-item previous"><a href="'. $url . 'page='. ($page - 1) .'" class="page-link">上一页</a></li>';
  }else{
    $tpl .= '<li class="page-item previous disabled"><span class="page-link">上一页</span></li>';
  }

  // 添加省略号
    if($page > ($adjacents + 2)) {
      $tpl .= '<li class=""><a href="'. $url . 'page=1" class="page-link">1</a></li>';
      $tpl .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
    }

  // 页码列表
  $pmin = ($page > $adjacents) ? ($page - $adjacents) : 1;
  $pmax = ($page < ($page_count - $adjacents)) ? ($page + $adjacents) : $page_count;
  for($i=$pmin; $i<=$pmax; $i++){
    if( $i == $page ){
      $tpl .= '<li class="page-item active"><span class="page-link">'. $i .'</span></li>';
    }else{
      $tpl .= '<li class=""><a href="'. $url . 'page='. $i .'" class="page-link">'. $i .'</a></li>';
    }
  }

  // 添加省略号
  if($page < ($page_count - $adjacents - 1 )) {
      $tpl .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
      $tpl .= '<li class=""><a href="'. $url . 'page='. $page_count .'" class="page-link">'. $page_count .'</a></li>';
  }

  // 下一页
  if($page < $page_count && $page_count > 1 ){
    $tpl .= '<li class="page-item next"><a href="'. $url . 'page='. ($page + 1) .'" class="page-link">下一页</a></li>';
  }else{
      $tpl .= '<li class="page-item next disabled"><span class="page-link">下一页</span></li>';
  }

  $tpl .= '</ul>';
  $tpl .= '</div>';
  $tpl .= '</div>';
	echo $tpl;

}
