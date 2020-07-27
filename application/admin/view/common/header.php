<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>内容管理中心</title>
    <link href="<?= __ADMIN__ ?>/js/bootstrap-3.3.7/css/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <link href="<?= __ADMIN__ ?>/css/common.css" rel='stylesheet' type='text/css' />
    <script type="text/javascript" src="<?= __ADMIN__ ?>/js/jquery-2.1.4.min.js"></script>
    <script type="text/javascript" src="<?= __ADMIN__ ?>/js/bootstrap-3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?= __ADMIN__ ?>/js/layer/layer.js"></script>
    <script type="text/javascript" src="<?= __ADMIN__ ?>/js/common.js"></script>
</head>
<body>
	<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <span class="navbar-brand">内容管理系统</span>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">个人中心 <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li class="dropdown-header">帐户：<?= $this->userinfo ?></li>
						<li role="separator" class="divider"></li>
						<li><a href="<?= url('admin/login/logout') ?>">退出登录</a></li>
					</ul>
				</li>
			</ul>
    </div>
  </div>
</nav>