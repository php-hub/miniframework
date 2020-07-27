<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="renderer" content="webkit">
  <meta http-equiv="Cache-Control" content="no-siteapp" />
  <title>內容管理系統</title>
  <link href="<?= __ADMIN__ ?>/css/login.css?v=2" rel='stylesheet' type='text/css' />
</head>
<body>
  <div class="wrapper">
    <div class="login-form">
      <div class="avtar">ADMIN LOGIN</div>
      <form name="myform" method="post">
        <div class="input-box"><input type="text" name="username"  placeholder="輸入帳號""></div>
        <div class="input-box"><input type="password" name="password"  placeholder="輸入密碼"></div>
        <div class="input-box captcha">
          <span><i></i><img id="captcha" title="点击刷新" onclick="this.src='<?= url('admin/login/captcha')?>&t='+Math.random();" src="<?= url('admin/login/captcha')?>"></img></span>
          <input type="text" name="captcha" placeholder="輸入右側驗證碼">
        </div>
        <div class="btn-signin"><button type="button" id="submit">登入</button></div>
      </form>
    </div>
  </div>
  <script type="text/javascript" src="<?= __ADMIN__ ?>/js/jquery-2.1.4.min.js"></script>
  <script type="text/javascript" src="<?= __ADMIN__ ?>/js/layer/layer.js"></script>
  <script type="text/javascript" src="<?= __ADMIN__ ?>/js/common.js"></script>
  <script>
  $("#submit").click(function(){

    if($("input[name='username']").val()==''){
      $("input[name='username']").focus();
      layer.alert("請輸入登錄帳號",{"icon":2});
      return false;
    }
    if($("input[name='password']").val()==''){
      $("input[name='password']").focus();
      layer.alert("請輸入密碼",{"icon":2});
      return false;
    }

    if($("input[name='captcha']").val()==''){
      $("input[name='captcha']").focus();
      layer.alert("請輸入驗證碼",{"icon":2});
      return false;
    }

    var callback = function(ret){
      if(ret.status === 1){
          window.location.href="<?= url("admin/index/index")?>";
      }else{
        layer.alert(ret.msg,{"icon":2});
      }
    }
    var obj = {'username':$("input[name='username']").val(),'password':$("input[name='password']").val(),'captcha':$("input[name='captcha']").val()};
    layer.msg("正在登錄，請稍後。",{icon:16,shade: 0.2,time:100000})
    ajax_post('<?= url("admin/login/check")?>',obj,callback);

  });
  </script>
</body>
</html>
