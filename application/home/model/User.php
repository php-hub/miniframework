<?php 
namespace app\home\model;
use \core\Model;
class User extends Model{

    public $errorMsg = '';
    private $talbeName = "user";
    
    public function add($data){
        $id = $this->db->insert($this->talbeName, $data);
        if( $id ){
            return $id; // return insert $id
        }else{
            $this->errorMsg = $this->db->getLastError();
            return false;
        }
        
    }

    public function getList(){
        $this->db->where("user_id",100);
        return $this->db->orderBy("user_id","desc")->get($this->talbeName); // if not finded , return array(0)
    }

    public function getOne($user_id){
        return $this->db->where("user_id",$user_id)->getOne($this->talbeName); // if not finded , return NULL
    }

    public function delete($user_id){
        $this->db->where("user_id",$user_id);
        if( $this->db->delete($this->talbeName) ){
            return $this->db->count; // return delete count
        }else{
            $this->errorMsg = $this->db->getLastError();
            return false;
        }
    }

    public function update($user_id, $data){
        $this->db->where("user_id",$user_id);
        if( $this->db->update($this->talbeName,$data) ){
            return $this->db->count; // return update count
        }else{
            $this->errorMsg = $this->db->getLastError();
            return false;
        }
        
    }

    public function getMsg(){
        return $this->errorMsg;
    }
}