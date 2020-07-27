<?php $this->load_template_part("common/header");?>
<div class="container">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="<?= url("admin/index/index") ?>">登記列表</a></li>
		<li role="presentation"><a href="<?= url("admin/index/shop") ?>" >商鋪列表</a></li>
		<li role="presentation"><a href="<?= url("admin/index/config") ?>">系統設置</a></li>
		<li role="presentation"><a href="<?= url("admin/index/password") ?>">設置密碼</a></li>
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
		<!-- 登記列表-->
		<div role="tabpanel" class="tab-pane active">
			<div class="row search-bar">
			<div class="col-sm-12 col-md-9">
				<form class="form-inline" method="get" action="">
				<div class="form-group">
					<label for="phone">手提電話：</label>
					<input type="number" class="form-control" value="<?= input("get.phone") ?>" id="phone" name="phone" placeholder="輸入手提電話">
				</div>
					<div class="form-group">
						<label for="email"> &nbsp; E-Mail：</label>
						<input type="text" id="email" autocomplete="off" class="form-control" value="<?= input("get.email") ?>" name="email" placeholder="輸入E-MAIL">
					</div>
					<button type="button" class="btn btn-info" id="search_submit">檢索</button>
					<button type="button" class="btn btn-link"  style="margin-left:10px;" onclick="window.location.href='<?= url("admin/index/index") ?>'">全部列表</button>
				</form>
			</div>
			<div class="col-sm-12 col-md-3 text-right export">
					<form action="<?= url("admin/index/export_excel") ?>" method="post" enctype="application/x-www-form-urlencoded">
						<button type="submit" class="btn btn-success"><i class="glyphicon glyphicon-save"></i> 下載EXCEL</button>
					</form>
				</div>
			</div>
			<div class="table-responsive">
				<!-- 超級管理員 -->
				<?php if( !empty($data["list"]) ): ?>
				<table class="table table-bordered table-hover">
					<thead>
					<tr>
						<th class="text-center">编号</th>
						<th class="text-center">姓名</th>
						<th class="text-center">手提號碼</th>
						<th class="text-center">電郵地址</th>
						<th class="text-center">店鋪名</th>
						<th class="text-center">登記時間</th>
						<th class="text-center">換領時間</th>
						<th class="text-center">刪除</th>
					</tr>
					</thead>
					<?php foreach( $data["list"] as $k => $v ): ?>
					<tr>
						<td class="text-center"><?= $v["record_id"] ?></td>
						<td><?= $v["name"] ?></td>
						<td>+<?= $v["area"] ?>-<?= $v["phone"] ?></td>
						<td><?= $v["email"] ?></td>
						<td><?= $v["shop_name"] ?></td>
						<td class="text-center"><?= date("Y-m-d h:i:s",$v["crt_time"]) ?></td>
						<td class="text-center"><?= ($v["redeem_time"]!=0)?date("Y-m-d h:i:s",$v["redeem_time"]):'' ?></td>
						<td class="text-center">
							<?php if( $v["redeem"] == 1 ): ?>
							<button type="button" class="btn btn-default btn-sm active" disabled="disabled"><i class="glyphicon glyphicon-trash"></i></button>
							<?php else:?>
							<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="<?= $v["record_id"] ?>" title="刪除"><i class="glyphicon glyphicon-trash"></i></button>
							<?php endif ?>

						</td>
					</tr>
					<?php endforeach; ?>
					
				</table>
				</div>
				<!-- 分頁 -->
				<div class="text-center">
					<!-- 分頁 -->
					<?php paginate( $data["total_count"], $data["limit"], input("get.page") , url("admin/index/index",["from"=>"user"]) ); ?>
				</div>
				<?php else: ?>
				<div class="panel panel-default">
					<div class="panel-body text-center">
						暂无记录
					</div>
				</div>
				<?php endif; ?>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){

	$(".btn-delete").on("click",function(){
		var record_id = $(this).attr("data-id");
		layer.prompt({title: '輸入確認密碼', formType: 1}, function(pass, index){
			if(pass === '123456'){
				//---
				var callback = function(data){
					layer.close(index);
					if(data.status === 1){
						layer.msg(data.msg,{"icon":1,time:1000},function(){
							layer.load(2);
							window.location.reload();
						});
					}else if(data.status === 1001){
						layer.alert(data.msg,{"icon":2},function(){
							window.location.href="<?= url("admin/login/index") ?>";
						});
					}else{
						layer.alert(data.msg,{"icon":2});
					}
				}

				layer.msg("正在刪除",{icon:16,shade: 0.2,time:100000})
				ajax_post('<?= url("admin/index/delete_record") ?>',{'record_id':record_id},callback);
				//---
			}else{
				layer.msg("確認密碼不正確!",{time:1500,icon:2});
			}
		});
	});


	$("#search_submit").click(function(){
		var phone = $("input[name='phone']").val(),
			email = $("input[name='email']").val();
		if(phone !='' || email !=''){
			layer.msg("正在提交，请稍后。",{icon:16,shade: 0.2,time:100000});
			window.location.href="<?= url("admin/index/index") ?>&phone="+ phone.trim() +"&email="+ email.trim();
		}

	});

});
</script>
<?php $this->load_template_part("common/footer");?>