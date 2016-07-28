<?php
namespace Common;
use Think\Model;
class CommonModel extends Model {
	
	//获取表字段
	function getTableFields(){
		if($this->fields) {
            $fields     =  $this->fields;
            return $fields;
        }
        return false;
	}
 
	
}
?>