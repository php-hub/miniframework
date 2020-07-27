<?php
class logModel extends model{

    // 保存日志
    public function save($data){
        $ret = $this->db->insert("log", $data);
        return $ret;
    }

}
