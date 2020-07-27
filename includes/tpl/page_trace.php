<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>温馨提示</title>
  <style>
  body{ margin: 0; font-size: 14px;  color: #636363;}
  .mainbox{ width: 90%; max-width: 500px; margin: auto; margin-top: 10%;}
  header{ background-color: #1D62F0; padding: 50px 0; text-align: center; color: #FFF; font-size: 1.5em;}
  .content{ border:1px solid #dedede; }
  .tips{ padding: 30px; line-height: 3em;}
  .error{ text-align: center; color: #CCC; font-size: 12px;}
  footer{ text-align: center; padding: 20px 0; font-size: 12px; color: #c09bb9;}
  ul{ margin-left: 20px; padding-left: 0; margin-top: 0;}
  a{ color: #1D62F0}
  </style>
</head>
<body>

<div class="mainbox">
  <header>天啊！页面出错了。</header>
  <div class="content">
    <div class="tips">
      您访问的页面罢工了。<br/>
      尝试如下操作：
      <ul>
        <li><a href="/">返回首页</a></li>
        <li><a href="javascript:window.history.go(-1);">回到上一页</a></li>
      </ul>
    </div>
    <div class="error">详细信息：<?php echo $msg; ?><br/></div>
    <footer>Powered by <a href="http://www.vipvip.cn">vipvip.cn</a></footer>
  </div>
</div>
</body>
</html>
