<?php $this->load_template_part("common/header");?>
<div class="container">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist" id="myTabs">
	<li role="presentation"><a href="<?= url("admin/index/index") ?>">登記列表</a></li>
		<li role="presentation"><a href="<?= url("admin/index/shop") ?>" >商鋪列表</a></li>
		<li role="presentation"><a href="<?= url("admin/index/config") ?>">系統設置</a></li>
		<li role="presentation" class="active"><a href="<?= url("admin/index/password") ?>">設置密碼</a></li>
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
	<!-- 系统配置 -->
		<form class="form-horizontal" id="configForm">
			<div role="tabpanel"  class="tab-pane active">
				<div class="search-bar text-right">
					<div class="btn-group" role="group">
						<button type="submit" class="btn btn-success" ><i class="glyphicon glyphicon-floppy-disk"></i> 保存設置</button>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">帳號及密碼設置</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">登錄帳號</label>
							<div class="col-sm-10">
							<input type="text" class="form-control" value="<?= $user_name ?>" name="username" placeholder="" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">新密碼</label>
							<div class="col-sm-10">
							<input type="password" class="form-control" value="" name="new_password" id="new_password" placeholder="" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">確認新密碼</label>
							<div class="col-sm-10">
							<input type="password" class="form-control" value="" name="re_new_password"  placeholder="" required>
							</div>
						</div>
					</div>
				</div>

			</div>
		</form>
	</div>
</div>
<script type="text/javascript" src="<?= __ADMIN__ ?>/js/jquery-validation-1.19.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?= __ADMIN__ ?>/js/jquery-validation-1.19.1/localization/messages_zh.min.js"></script>
<script type="text/javascript" src="<?= __ADMIN__ ?>/js/laydate/laydate.js"></script>
<script>

$(document).ready(function(){

	var validator = $("#configForm").validate({
		rules: {
			re_new_password: {
				equalTo: "#new_password"
			}
		},
		messages: {
			re_new_password: {
				equalTo: "两次密码输入不一致"
			}
		},
		errorElement: "span",
		submitHandler: function(form) {
			var data = $("#configForm").serialize();
			var url = "<?= url("admin/index/save_password") ?>";
			layer.msg("正在保存，请稍后。",{icon:16,shade: 0.2,time:100000})
			var callback = function(data){
				layer.closeAll();
				if(data.status === 1){
					layer.msg(data.msg,{"icon":1,time:1000},function(){
						window.location.href = "<?= url('admin/login/index')?>";
					});
				}else{
					layer.alert(data.msg,{"icon":2});
				}
			}
			ajax_post(url, data ,callback);
			return;
		}
	});

});
</script>
<?php $this->load_template_part("common/footer");?>