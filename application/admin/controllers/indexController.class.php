<?php

class indexController extends commonController{

  public $shopModel;
  public $recordModel;
  public $configModel;
  public $logModel;

  public function __construct(){
    parent::__construct();
    
    $this->shopModel = new shopModel;
    $this->recordModel = new recordModel;
    $this->configModel = new configModel;
    $this->logModel = new logModel;
  }

  /*----------------
    登記管理
  ----------------*/

  // 登記列表页
  public function index(){
    $page = input("get.page")?input("get.page"):1; // 当前页
    $limit = 10; // 每页显示行数
    $phone = input("get.phone");
    $email = input("get.email");

    $ret = $this->recordModel->get_record($page, $limit, $phone, $email);
    $this->assign("data",$ret);
    $this->render();
  }


  // 删除登記
  public function delete_record(){
    $record_id = input("post.record_id");
    $ret = $this->recordModel->delete($record_id);
    if($ret){ 
      // 給相應的店鋪增加名額
      $shop_info = $this->recordModel->get_record_info($record_id);
      $this->shopModel->update_stock($shop_info["shop"],"inc");

      $this->log("delete_record","刪除登記。record_id=". $record_id);
      ajax_return(['status'=>1,"msg"=>'删除成功！']);
    }else{
      ajax_return(['status'=>0,"msg"=>'删除失败']);
    }
  }

  // 店鋪列表
  public function shop(){
    $ret = $this->shopModel->get_shop();
    $this->assign("list",$ret);
    $this->render();
  }

  // 獲取店鋪資料
  public function get_shopinfo(){
    $shop_id = input("post.shop_id");
    $ret = $this->shopModel->get_shopinfo($shop_id);
    ajax_return(['status'=>1,"msg"=>'ok',"data"=>$ret]);
  }

  // 增加/修改店鋪
  public function edit_shop(){
    $post = input("post.");
    $data["shop_name"]    = $post["shop_name"];
    $data["shop_address"] = $post["shop_address"];
    $data["shop_tel"]     = $post["shop_tel"];
    $data["quota"]        = $post["quota"];
    if( empty($post["shop_id"]) ){
      $data["stock"] = $post["quota"];
      $ret = $this->shopModel->add($data);
      if($ret){
        $this->log("add_shop","店鋪增加成功");
        ajax_return(['status'=>1,"msg"=>'保存成功']);
      }else{
        $this->log("add_shop","店鋪增加失敗");
        ajax_return(['status'=>0,"msg"=>'保存失敗']);
      }
    }else{
      $log_type = "edit_shop";
      // 判斷是否有修改總名額
      $shop_info = $this->shopModel->get_shopinfo( $post["shop_id"] );
      if( intval($data["quota"]) > intval($shop_info["quota"]) ){
        $data["stock"] = $shop_info["stock"] + ( intval($data["quota"]) - intval($shop_info["quota"]));
      }else if( intval($data["quota"]) < intval($shop_info["quota"])){
        ajax_return(['status'=>0,"msg"=>'總名額數量不正確']);
      }

      $ret = $this->shopModel->edit($post["shop_id"], $data);
      if($ret){
        $this->log("edit_shop","店鋪修改成功");
        ajax_return(['status'=>1,"msg"=>'保存成功']);
      }else{
        $this->log("edit_shop","店鋪修改失敗");
        ajax_return(['status'=>0,"msg"=>'保存失敗']);
      }
      
    }
  }  

  // 删除店鋪
  public function shop_delete(){
    $shop_id = input("post.shop_id");
    $ret = $this->shopModel->delete($shop_id);
    if($ret){
      $this->log("delete_shop","刪除店鋪。shop_id=". $shop_id);
      ajax_return(['status'=>1,"msg"=>'删除成功！']);
    }else{
      ajax_return(['status'=>0,"msg"=>'删除失败']);
    }
  }



  /*----------------
    系统配置
  ----------------*/
  public function config(){
    $ret = $this->configModel->get_config('config');
    if( $ret ){
      $data = json_decode($ret["value"], true);
    }else{
      $data = '';
    }

    $this->assign("data",$data);
    $this->render();
  }

  // 保存配置
  public function save_config(){
    $post = input("post.");
    if( empty( $post ) ){
      ajax_return(['status'=>0,"msg"=>'数据不能为空']);
    }
    $value = json_encode($post);
    $ret = $this->configModel->save('config',$value);
    if($ret){
      ajax_return(['status'=>1,"msg"=>'保存成功！']);
    }else{
      ajax_return(['status'=>0,"msg"=>'保存失败']);
    }
  }

  /*----------------
  修改密码
  ----------------*/
  public function password(){

    $this->render();
  }

  public function save_password(){
    $post = input("post.");
    if( empty( $post ) ){
      ajax_return(['status'=>0,"msg"=>'数据不能为空']);
    }
    $data["user"] = $post["username"];
    $data["password"] = md5(ADMIN_USER_KEY.$post["new_password"]);
    $value = json_encode($data);
    $ret = $this->configModel->save('user',$value);
    if($ret){
      $_SESSION['user_name'] = '';
      ajax_return(['status'=>1,"msg"=>'修改成功，請使用新密碼登錄！']);
    }else{
      ajax_return(['status'=>0,"msg"=>'保存失败']);
    }
  }

  /*----------------
    輸出數據到EXCEL
  ---------------*/
  public function export_excel(){
    $this->load_extend("excel"); // 載入EXCEL類

    // config info 
    $config_ret = $this->configModel->get_config('config');
    $config = json_decode($config_ret["value"], true);

    ob_clean(); // 清缓存
    extract($_POST);
    $xls = new Excel_XML("UTF-8", false, $config["short_url_acode"]."report");
    
    $data = array(
            array("Record ID",
                  "Name",
                  "Phone",
                  "Shop",
                  "utm source",
                  "utm medium",
                  "utm campaign",
                  "Create Date",
                  "Create IP",
                  "Redeem Status",
                  "Redeem Time",
                  "Redeem IP",
                  "Redeem URL",
                  "News Letter"
                )
    );
    $result = $this->recordModel->export_data();

    foreach($result as $key=>$value){
      $redeem_status = ($value["redeem"]==1)?"Yes":"No";
      $redeem_time = ($value["redeem"]==1)?date("Y-m-d H:i:s",$value["redeem_time"]):"";
      $newsletter = ($value["newsletter"]==1)?"Yes":"No";
      $utm_data = explode("|",$value["utm"]);
      array_push($data,
        array($value["record_id"],
              $value["name"],
              $value["area"].'-'.$value["phone"],
              $value["shop_name"],
              $utm_data[0],
              $utm_data[1],
              $utm_data[2],
              date("Y-m-d H:i:s",$value["crt_time"]),
              $value["crt_ip"],
              $redeem_status,
              $redeem_time,
              $value["redeem_ip"],
              "http://hkfb.cc/".$value["code"].$config["short_url_prefix"].$config["short_url_sitecode"],
              $newsletter
            )
      );
    }
    $xls->addArray($data);
    $xls->generateXML($config["short_url_acode"].'_'.time());

  }



}
