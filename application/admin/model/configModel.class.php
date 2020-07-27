<?php
class configModel extends model{

    // 获取配置
    public function get_config($name){
        $ret = $this->db->where("name", $name)->getOne("config");
        return $ret;
    }

    // 保存配置
    public function save($name,$value){
        $check = $this->db->where("name",$name)->getOne("config");
        $data["value"] = $value;
        if(!$check){
            // 新增
            $data["name"] = $name;
            $ret = $this->db->insert("config", $data);
            if(!$ret){
                return  false;
            }
        }else{
            // 更新
            $ret = $this->db->where("name", $name)->update("config",$data);
            if(!$ret){
                return  false;
            }
        }
        return $ret;
    }

}
