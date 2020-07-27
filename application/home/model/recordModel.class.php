<?php
class contentModel extends model{

  // 内容列表
  public function get_content($client_id){
    if(!empty($client_id)){
      $ret = $this->db->orderBy("content_id","DESC")->where("client_id",$client_id)->get("content", null);
    }else{
      $ret = false;  
    }
    return $ret;
  }


}
