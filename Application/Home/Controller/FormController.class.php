<?php
namespace Home\Controller;
use Common\CommonController;

class FormController extends CommonController {
	protected $nodeId = [];	
	protected $autoInstantiateModel = false;
	protected $options = [];
	//protected $pre = "rrbrr_";
 
	
	 public function index(){
	 	$tableNameList = getTableNameList();
	 	//$this->tabText = $tableNameList;
	 	//$this->tabText = $tableNameList;
	 	$this->tabText = array_combine($tableNameList, $tableNameList);
	 	$this->display();
		
    }
    
    function generate(){
    	$tableName = I('tableName');
    	$columnNameKey = strtoupper(getColumnNameKey());
    	$str = '';
    	$selectedFields = I('tableFields');
    	if(empty($tableName)){
    		$this->generateAll();
    		return;
    	}else{
    		$allFields = getTableInfoArray($tableName);
    	}
    	$str .='<form class="form-horizontal" role="form"  method="post" action="__URL__/save/">';
    	foreach($allFields as $columnInfo){
    		if(!empty($selectedFields) && !in_array($columnInfo['COLUMN_NAME'], $selectedFields)) continue;
    		$str .= $this->createFormRow($columnInfo);
    		//$str .= '<option value="'.$columnInfo[$columnNameKey].'" >'.$columnInfo[$columnNameKey]."</option>\r\n";
    	}
    	$str .= '<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default">保存</button>
    </div>
  </div>';
    	$str .='</form>';
    	echo $str;
    	
    	echo "\n\n\n\n";
    	foreach ($this->options as $k => $v){
    		echo '"'.$tableName.'_'.$k.'"',"=>",$v,"\n\n";
    	}
    	
    }
    
    //生成所有表的form
    function generateAll(){
    	$tableNameList = getTableNameList();
		foreach($tableNameList as $k => $tableName){
			$tableInfoArray = getTableInfoArray($tableName);
			$columnNameKey = strtoupper(getColumnNameKey());
			$str = '';
			
			$str .='<form class="form-horizontal" role="form">';
			foreach($tableInfoArray as $columnInfo){
				//var_dump($columnInfo);exit;
				 $str .= $this->createFormRow($columnInfo);
				//$str .= '<option value="'.$columnInfo[$columnNameKey].'" >'.$columnInfo[$columnNameKey]."</option>\r\n";
			}
			$str .="</form>\n\n\n\n";
			echo $str;
			//echo "<hr />";
		}
		

 
    }
	
	//获取字段类型及长度
	function getColumnType($type){
		//$type = "text";
		$typeInfo = [];
		if(strpos($type,"(") !== false){
			if(preg_match("/(\w+)\((\d+)\)/",$type,$matches)){
				
				$typeInfo["type"] = $matches[1];
				$typeInfo['size'] = $matches[2];
				
				//return $matches[0];
			}elseif(preg_match("/(\w+)\((.+)\)/",$type,$matches)){
				$typeInfo["type"] = $matches[1];
				$typeInfo['size'] = $matches[2];
			}
			
		}else{ //text
			$typeInfo["type"] = $type;
			$typeInfo['size'] = 65535;
		}
		return $typeInfo;
		
	}
	
	//解析注释获取name和选项
	function parserComment($comment){
		//$id=$name='status';
		$ret = [];
		//$comment = '状态|0:禁用,1:正常,2:待审核';
		$arr = explode("|",$comment);
		
		$name = explode("-",$arr[0]);
		$ret['name'] = $name[0];
		if(!empty($name[1])){ //有name后指定类型如 status-checkbox则用后面的类型，否则用select
			$ret["type"] = $name[1];
		}
 		
		if(!empty($arr[1])){
		
			$ret["type"] = "select";
		
			$items = explode(",",$arr[1]);
			$options = [];
			foreach($items as $item){
				list($value, $text) = explode(':',$item);
				
				$options[$value] = "$text";
			}
			$ret["options"] = $options;
		}
		return $ret;
		//$select = "<select id=\"$id\" name=\"$name\">$options</select>";
		//$form_row = $label.$select;
		//echo $form_row;
	}
	
	//根据一个字段信息创建一个表单项
	function createFormRow($columnInfo){
		$inputAttribute = [];
		$typeInfo = $this->getColumnType($columnInfo['COLUMN_TYPE']);
		$type = strtoupper($columnInfo['type']);
		if(in_array($type,["TINYINT","SMALLINT","MEDIUMINT","INT","BIGINT","FLOAT","DOUBLE","DECIMAL"])){ //数字类型
			$inputAttribute['type'] = "text";
			$inputAttribute['size'] = 10;
		}elseif(in_array($type,["DATE","TIME","YEAR","DATETIME","TIMESTAMP"])){ //日期类型
			$inputAttribute['type'] = "date";
		}elseif(in_array($type,["CHAR","VARCHAR","TINYBLOB","TINYTEXT"])){ //小文本
			$inputAttribute['type'] = "text";
			$inputAttribute['size'] = 30;
			if( $type == "varchar" && $columnInfo['size'] >255 ){ //大文本域
				$inputAttribute['type'] = "textare";
				$inputAttribute['row'] = 10;
			}
	 	}elseif(in_array($type,["BLOB","TEXT","MEDIUMBLOB","MEDIUMTEXT","LONGBLOB","LONGTEXT"])){
			$inputAttribute['type'] = "textare";
			$inputAttribute['row'] = 10;
		}else{
			$inputAttribute['type'] = "text";
			$inputAttribute['size'] = 30;
		}
		
		
		$commentInfo = $this->parserComment($columnInfo['COLUMN_COMMENT']);
		if(!empty($commentInfo['options'])){
			$inputAttribute['type'] = "text";
		}
		
		
		$cnName = empty($commentInfo['name']) ? $columnInfo['COLUMN_NAME'] : $commentInfo['name'];
		$name = $columnInfo['COLUMN_NAME'];
		$inputStr = "";
		$confStr = "";
		
		if(!empty($commentInfo['type'])){
			if($commentInfo['options']){
				$this->options[$columnInfo['COLUMN_NAME']] = var_export($commentInfo['options'],1);
				if($commentInfo['type'] == "select"){
					$inputStr .= "<html:select options='opt_status' selected='status_selected' name=\"{$columnInfo['COLUMN_NAME']}\" />";
					$inputStr .= " <select name=\"select\" id=\"select\">";
					foreach($commentInfo['options'] as $value => $text){
						$inputStr.="<option value=\"{$value}\">$text</option>";
					}
					$inputStr .= "</select>";
					
				}elseif($commentInfo['type'] == "radio"){
					
					foreach($commentInfo['options'] as $value => $text){
						$inputStr .= "<input name=\"select\" id=\"select\" type=\"radio\"  value=\"$value\">{$text} |";
					}
					
				}elseif($commentInfo['type'] == "checkbox"){
					foreach($commentInfo['options'] as $value => $text){
						$inputStr .="  <input name=\"select\" id=\"select\"  type=\"checkbox\" value=\"$value\">{$text} |";
					}
					
					//$inputStr = "<input name=\"$name\" type=\"text\" id=\"$name\" size=\"{$inputAttribute['size']}\" />";
				}
			}
			
			
		}else{
			if($inputAttribute['type'] == "text"){
				//<textarea name="textarea" cols="30" rows="10" id="textarea"></textarea>
				$inputStr .= "<input name=\"$name\" type=\"text\" id=\"$name\" size=\"{$inputAttribute['size']}\" value=".'"{$vo.'.$name.'}"'." />";
			}elseif($inputAttribute['type'] == "textare"){
				$inputStr .= "<textarea name=\"$name\" cols=\"30\" rows=\"10\" id=\"$name\"></textarea>";
			}	
		}
		 return '<div class="form-group">
    <label for="'.$name.'" class="col-sm-2 control-label">'.$cnName.'</label>
    <div class="col-sm-10">
       '.$inputStr.'
    </div>
  </div>';
		 
		
		 
	}
	
	public function generateCreatFormCode(){
		$templateFilePath = MODULE_PATH. "Template/View/formCode.html";
		$formMethod = I('formMethod');
		$formAction = I('formAction');
		$this->assign('formMethod', $formMethod);
		$this->assign('formAction', $formAction);
		$resultCode = $this->fetch($templateFilePath);
		return $resultCode;
	}
	
	public function creatForm(){
		echo $this->generateCreatFormCode(); 
	}
	
	public function loadField(){
		$tableName = I('tableName');
		$tableInfoArray = getTableInfoArray($tableName);
		$columnNameKey = strtoupper(getColumnNameKey());
		$str = '';
		foreach($tableInfoArray as $tableInfo){
			$str .= '<option value="'.$tableInfo[$columnNameKey].'" >'.$tableInfo[$columnNameKey]."</option>\r\n";
		}
		echo $str;
	}
	
	
    function index2($id=""){
    	$k = I('k');
    	$map = $where = [];
    	$mApi = M('rrbrr_doc','','api');
    	if($k){
    		$where['title']  = array('like', "%{$k}%");
    		$where['url'] =  array('like', "%{$k}%");
    		$where['_logic'] = 'or';
    		$map['_complex'] = $where;
    		$list = $mApi->where($map)->order("id desc")->select();
    		$this->list = $list;
    		$this->display();
    		return ;
    	}
    	
    	$list = $mApi->where($map)->order("update_time desc")->select();
    	$newList = [];
    	foreach ($list as $k => $v) {
    		$newList[$v['module']][] = $v;
    	}
    	$num = I('num');
    	if($num != null){
    		
    		$a = [];
    		$a[$num] = $newList[$num];
    		unset($newList[$num]);
    		//array_unshift($newList,$current);
    		
    		$newList = array_merge($a, $newList);
    		
    	}
    	
    	//$newList[0]
    	$this->list = $newList;
    	$mApi = M('rrbrr_doc','','api');
    	$this->detail = $mApi->find($id);
    	
    	
		$this->display();
	}
	
	function _before_save(){
		C('DEFAULT_FILTER',"");
		$_POST['method'] = strtoupper($_POST['method']);
		$_POST['update_time'] = time();
	}
	
	function show($id){
		$mApi = M('rrbrr_doc','','api');
		$this->vo = $mApi->find($id);
		$this->display();
	}
	
	 
	 

}




