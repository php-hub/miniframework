<?php 
namespace app\home\model;
use \core\Model;
class User extends Model{
    public function add($user_name){
        $data["user_name"] = $user_name;
        return $this->db->insert("user", $data);
    }

    public function getList(){
        return $this->db->orderBy("user_id","desc")->get("user");
    }

    public function getOne($user_id){
        return $this->db->where("user_id",$user_id)->getOne("user");
    }

    public function delete($user_id){
        return $this->db->where("user_id",$user_id)->delete("user");
    }
}