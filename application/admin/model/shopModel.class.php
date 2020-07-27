<?php
class shopModel extends model{

  // 店鋪列表
  public function get_shop(){
	$ret = $this->db->orderBy("sort","ASC")->orderBy("shop_id","ASC")->get("shop",null);
	return $ret;
  }

  // 獲取店鋪內容
  public function get_shopinfo($shop_id){
    $ret = $this->db->where("shop_id",$shop_id)->getOne("shop");
    return $ret;
  }

  // 增加店鋪
  public function add($data){
    $ret = $this->db->insert("shop",$data);
    return $ret;
  }

  // 修改店鋪
  public function edit($shop_id, $data){
    $ret = $this->db->where("shop_id",$shop_id)->update("shop",$data);
    return $ret;
  }


  // 删除内容
  public function delete($shop_id){
    $ret = $this->db->where("shop_id",$shop_id)->delete("shop");
    return $ret;
  }

  // 更新库存 inc(+)，dec(-)
	public function update_stock($shop_id, $action){
		if($action === 'inc'){
			$data = Array ('stock' => $this->db->inc(1));
		}else if($action === 'dec'){
			$data = Array ('stock' => $this->db->dec(1));
		}

		// 锁stock表
		$this->db->setLockMethod("WRITE")->lock("shop");
		$rs = $this->db->where("shop_id",$shop_id)->update("shop", $data);
		$this->db->unlock();
		if($rs){
			$res = true;
		}else{
			$res = false;
		}
		return $res;
	}
  
}
