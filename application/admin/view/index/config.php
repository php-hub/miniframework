<?php $this->load_template_part("common/header");?>
<div class="container">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist" id="myTabs">
	<li role="presentation"><a href="<?= url("admin/index/index") ?>">登記列表</a></li>
		<li role="presentation"><a href="<?= url("admin/index/shop") ?>" >商鋪列表</a></li>
		<li role="presentation" class="active"><a href="<?= url("admin/index/config") ?>">系統設置</a></li>
		<li role="presentation"><a href="<?= url("admin/index/password") ?>">設置密碼</a></li>
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
						<h3 class="panel-title">時間設置</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">登記截止時間</label>
							<div class="col-sm-10">
							<input type="text" class="form-control" value="<?= isset($data["game_deadline"])?$data["game_deadline"]:'' ?>" name="game_deadline" id="game_deadline" placeholder="" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">換領截止時間</label>
							<div class="col-sm-10">
							<input type="text" class="form-control" value="<?= isset($data["redeem_deadline"])?$data["redeem_deadline"]:'' ?>" name="redeem_deadline" id="redeem_deadline" placeholder="" required>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">短網址設置</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">PREFIX</label>
							<div class="col-sm-10">
							<input type="text" class="form-control"  value="<?= isset($data["short_url_prefix"])?$data["short_url_prefix"]:'' ?>" name="short_url_prefix" placeholder="" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">SITECODE</label>
							<div class="col-sm-10">
							<input type="text" class="form-control"  value="<?= isset($data["short_url_sitecode"])?$data["short_url_sitecode"]:'' ?>" name="short_url_sitecode" placeholder="" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">ACODE</label>
							<div class="col-sm-10">
							<input type="text" class="form-control"  value="<?= isset($data["short_url_acode"])?$data["short_url_acode"]:'' ?>" name="short_url_acode" placeholder="" required>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">短訊設置</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="col-sm-2 control-label">簽名</label>
							<div class="col-sm-10">
								<input type="text" class="form-control"  value="<?= isset($data["sms_orgadd"])?$data["sms_orgadd"]:'' ?>" name="sms_orgadd" placeholder="" required>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">短訊模板</label>
							<div class="col-sm-10">
								<textarea class="form-control" name="sms_content" placeholder="" required rows="5"><?= isset($data["sms_content"])?$data["sms_content"]:'' ?></textarea>
								<span class="help-block">變量說明（ 店鋪名：{$shopName} ；換領網址：{$redeemUrl} ）</span>
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
	laydate.render({
		elem: '#game_deadline'
		,lang: 'en'
	}); 
	laydate.render({
		elem: '#redeem_deadline'
		,lang: 'en'
	}); 

	var validator = $("#configForm").validate({
		errorElement: "span",
		submitHandler: function(form) {
			var data = $("#configForm").serialize();
			var url = "<?= url("admin/index/save_config") ?>";
			layer.msg("正在保存，请稍后。",{icon:16,shade: 0.2,time:100000})
			var callback = function(data){
				layer.closeAll();
				if(data.status === 1){
					layer.msg(data.msg,{"icon":1,time:1000},function(){
						window.location.reload();
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