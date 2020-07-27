<?php $this->load_template_part("common/header");?>
<div class="container">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation"><a href="<?= url("admin/index/index") ?>">登記列表</a></li>
		<li role="presentation" class="active"><a href="<?= url("admin/index/shop") ?>" >商鋪列表</a></li>
		<li role="presentation"><a href="<?= url("admin/index/config") ?>">系統設置</a></li>
		<li role="presentation"><a href="<?= url("admin/index/password") ?>">設置密碼</a></li>
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
		<!-- 店鋪列表-->
		<div role="tabpanel" class="tab-pane active">
			<div class="search-bar text-right">
				<div class="btn-group" role="group">
					<button type="button" class="btn btn-success btn-add"><i class="glyphicon glyphicon-plus-sign"></i> 增加店鋪</button>
				</div>
			</div>

			<div class="table-responsive">
				<!-- 超級管理員 -->
				<?php if( !empty($list) ): ?>
				<table class="table table-bordered table-hover">
					<thead>
					<tr>
						<th class="text-center">编号</th>
						<th class="text-center">店鋪名稱</th>
						<th class="text-center">總名額</th>
						<th class="text-center">剩餘名額</th>
						<th class="text-center">操作</th>
					</tr>
					</thead>
					<?php foreach( $list as $k => $v ): ?>
					<tr>
						<td class="text-center"><?= $v["shop_id"] ?></td>
						<td><?= $v["shop_name"] ?></td>
						<td><?= $v["quota"] ?></td>
						<td><?= $v["stock"] ?></td>
						<td class="text-center">
							<div class="btn-group" role="group">
								<button type="button" class="btn btn-info btn-sm btn-edit" data-id="<?= $v["shop_id"] ?>" title="修改"><i class="glyphicon glyphicon-pencil"></i></button>
								<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="<?= $v["shop_id"] ?>" title="刪除"><i class="glyphicon glyphicon-trash"></i></button>
							</div>
						</td>
					</tr>
					<?php endforeach; ?>
					
				</table>
				<?php endif; ?>
			</div>
				
		</div>
	</div>

</div>
<!-- edit tpl -->
<div id="edit_popup_tpl" style="display:none;">
	<form class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-2 control-label">店鋪名稱</label>
		<div class="col-sm-10">
		<input type="text" class="form-control" name="shop_name">
		</div>
	</div>
	<div class="form-group">
		<label  class="col-sm-2 control-label">店鋪地址</label>
		<div class="col-sm-10">
		<input type="text" class="form-control" name="shop_address">
		</div>
	</div>
	<div class="form-group">
		<label  class="col-sm-2 control-label">聯系電話</label>
		<div class="col-sm-10">
		<input type="text" class="form-control" name="shop_tel">
		</div>
	</div>
	<div class="form-group">
		<label  class="col-sm-2 control-label">總名額</label>
		<div class="col-sm-10">
		<input type="text" class="form-control" name="quota">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="shop_id">
		<button type="button" class="btn btn-info" id="edit_submit">保存資料</button>
		</div>
	</div>
	</form>
</div>

<script>
$(document).ready(function(){
	// 刪除店鋪
	$(".btn-delete").on("click",function(){
		var shop_id = $(this).attr("data-id");
		layer.prompt({title: '請輸入確認密碼', formType: 1}, function(pass, index){
			if(pass === '123456'){
				//---
				var callback = function(data){
					layer.close(index);
					if(data.status === 1){
						layer.msg(data.msg,{"icon":1,time:1000},function(){
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
				ajax_post('<?= url("admin/index/shop_delete") ?>',{'shop_id':shop_id},callback);
				//---
			}else{
				layer.msg("密碼不正確!",{time:1500,icon:2});
			}
		});
	});

	// 增加店鋪
	$(".btn-add").click(function(){
		$("input[name='shop_id']").val('');
		$("input[name='shop_name']").val('');
		$("input[name='shop_address']").val('');
		$("input[name='shop_tel']").val('');
		$("input[name='quota']").val('');
		$("input[name='shop_id']").val('');
		layer.open({
			type:1,
			title: '增加新店鋪',
			area: ['620px', '350px'], //宽高
			content: $("#edit_popup_tpl")
		});
	});

	// 修改店鋪
	$(".btn-edit").on("click",function(){
		var callback = function(ret){
			layer.closeAll();
			if(ret.status === 1){
				var data = ret.data;
				$("input[name='shop_id']").val(data.shop_id);
				$("input[name='shop_name']").val(data.shop_name);
				$("input[name='shop_address']").val(data.shop_address);
				$("input[name='shop_tel']").val(data.shop_tel);
				$("input[name='quota']").val(data.quota);
				layer.open({
					type:1,
					title: '修改店鋪資料',
					area: ['620px', '350px'], //宽高
					content: $("#edit_popup_tpl")
				});
			}else{
				layer.alert("獲取數據失敗",{"icon":2});
			}
		}
		layer.msg("请稍后",{icon:16,shade: 0.2,time:100000})
		ajax_post('<?= url("admin/index/get_shopinfo") ?>',{shop_id:$(this).attr("data-id")},callback);
	});

	// 提交
	$("#edit_submit").click(function(){
		var shop_name 		= $("input[name='shop_name']").val(),
			shop_address 	= $("input[name='shop_address']").val(),
			shop_tel 		= $("input[name='shop_tel']").val(),
			quota 			= $("input[name='quota']").val(),
			shop_id 		= $("input[name='shop_id']").val();
		if(shop_name && shop_address && quota){
			var obj = {shop_name:shop_name, shop_address:shop_address, shop_tel:shop_tel, quota:quota, shop_id:shop_id};
			var callback = function(data){
				if(data.status === 1){
					layer.msg(data.msg,{"icon":1,time:1000},function(){
						window.location.reload();
					});
				}else{
					layer.alert(data.msg,{"icon":2});
				}
			}
			layer.msg("正在保存",{icon:16,shade: 0.2,time:100000})
			ajax_post('<?= url("admin/index/edit_shop") ?>',obj,callback);

		}else{
			layer.alert("請完整輸入資料");
		}

	});

});
</script>
<?php $this->load_template_part("common/footer");?>