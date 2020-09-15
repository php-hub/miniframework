/**

 @Name：layuiAdmin 公共业务
 @Author：贤心
 @Site：http://www.layui.com/admin/
 @License：LPPL
    
 */
 
layui.define(function(exports){
  var $ = layui.$
  ,layer = layui.layer
  ,laytpl = layui.laytpl
  ,setter = layui.setter
  ,view = layui.view
  ,admin = layui.admin
  
  //公共业务的逻辑处理可以写在此处，切换任何页面都会执行
  //……
  
  //退出
  admin.events.logout = function(){
    //执行退出接口
    admin.req({
      url: layui.setter.adminSrc +'user/logout'
      ,type: 'get'
      ,data: {}
      ,done: function(res){
        if(res.status == 1){
          //清空本地记录的 token，并跳转到登入页
          admin.exit();
        }else{
          layer.alert("退出失败，请重试。",{icon:2});
        }
        
      }
    });
  };
  
  //对外暴露的接口
  exports('common', {
    // 编辑器
    editor: function(){
      layer.msg("this is editor");
    }
  });
});