<?php
class recordModel extends model{

  // 登記列表
  public function get_record($page, $limit = 10, $phone = '', $email = ''){
    if( !empty($phone) ){
			$this->db->where ("r.phone", $phone);
		}
		if( !empty($email) ){
      $this->db->where ("r.email", $email);
		}
    $this->db->pageLimit = $limit;
    $this->db->join("shop"." s", "s.shop_id = r.shop", "LEFT");
    $this->db->where ("r.deleted", 0);
		$results = $this->db->withTotalCount()->orderBy("r.record_id","DESC")->paginate("record r", $page, "s.shop_name, r.*");
		$data["total_pages"] = $this->db->totalPages;
    $data["list"] = $results;
    $data["limit"] = $limit;
		$data["total_count"] = $this->db->totalCount;
    return $data;
    
  }

  // 獲取登記信息
  public function get_record_info($record_id){
    $ret = $this->db->where("record_id",$record_id)->getOne("record");
    return $ret;
  }


  // 删除内容
  public function delete($record_id){
    $ret = $this->db->where("record_id",$record_id)->update("record", ["deleted" => 1]);
    return $ret;
  }

  	// 導出EXCEL
	public function export_data(){
		$this->db->join("shop" ." s", "s.shop_id = r.shop", "LEFT");
		$this->db->orderBy("r.record_id","desc");
		$res = $this->db->where("r.deleted",0)->get("record"." r" , NULL , "r.*, s.shop_name");
		return $res;
	}
  
}
