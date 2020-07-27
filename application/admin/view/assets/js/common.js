// AJAX POST
function ajax_post(url,obj,callback){
  $.ajax({
    url: url,
    cache: false,
    data: obj,
    type: "POST",
    dataType: "json",
    success: function(data){
      callback(data);
    },
    error:function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest);
      console.log(textStatus);
      console.log(errorThrown);
    }
  });
}