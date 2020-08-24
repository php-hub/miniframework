<?php
namespace app\home\controllers;
use \core\Controller;

class Common extends Controller{

  public $BOCpay;
  public $orderInfo;
  // 构造方法
  protected function __construct(){
    // 支付平台信息
    $this->BOCpay = [
      "merchantId" => "555618080003400", // 商户编号
      "terminalNo" => "98030663",        // 线上API终端号
      "notifyUrl"  => "http://wechat.macaulotustv.com/dwqyds2020/notify",  // 支付完成服务端通知URL
      "pageUrl"    => "http://wechat.macaulotustv.com/dwqyds2020/success" // 支付完成跳转URL
    ];

    // 订单信息
    $this->orderInfo = [
      "subject" => "大湾区优等声报名",
      "amount"  => "訂單總金額"
    ];

  }

  // 返回JSON
  protected function ajaxReturn($data){
    echo json_encode($data);
  }
}