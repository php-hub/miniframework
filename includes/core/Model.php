<?php
namespace core;
use \core\MysqliDb;

class Model{
  public $db = null;
  public function __construct(){
    $this->conn(); // 连接数据库
  }
  // 连接数据库
  private function conn(){
    if($this->db !== null) {
        return $this->db;
    }
    try {
      $res = new MysqliDb(Array (
            'host' => DB_HOST,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'db'=> DB_NAME,
            'prefix' => DB_PREFIX,
            'charset' => 'utf8'));
      $this->db = $res;
    } catch (PDOException $e) {
        exit($e->getMessage());
    }
    
  }
}
