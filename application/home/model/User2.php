<?php 
namespace app\home\model;
use \core\Model;
class User2 extends Model{
    public function getList(){
        return $this->db->orderBy("user_id","desc")->get("user");
    }
}