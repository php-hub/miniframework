<?php
class recordModel extends model{

    // 获取配置
    public function get_config($client_id, $name = 'config'){
        $ret = $this->db->where("client_id", $client_id)->where("name",$name)->getOne("config");
        return $ret;
    }
}
