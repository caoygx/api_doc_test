<?php
namespace Home\Controller;
use Common\CommonController;
use Common\TableInfo;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class FormController extends CommonController {
	protected $nodeId = [];	
	protected $autoInstantiateModel = false;
	protected $options = []; //存储数组序列化后的字符串格式
    protected $arrOptions = []; //存储数组格式
	//protected $pre = "lez_";
 
	
	 public function index(){
	    $tableInfo=new TableInfo();
	 	$tableNameList = $tableInfo->getTableNameList();
	 	//$this->tabText = $tableNameList;
	 	//$this->tabText = $tableNameList;
	 	$this->tabText = array_combine($tableNameList, $tableNameList);
	 	$this->display();
		
    }
    
    function preview(){
        $this->display("tpl_preview");
    }
    
    function generate(){
    	$tableName = I('tableName');
    	$columnNameKey = getColumnNameKey();
    	$str = '';
    	$selectedFields = I('tableFields');
    	if(empty($tableName)){
    		$this->generateAll();
    		return;
    	}else{
    	    if(is_array($tableName)) $tableName = $tableName[0];
            $tableInfo = new TableInfo();
    		$allFields = $tableInfo->getTableInfoArray($tableName);
    	}

        $tableInfo = new TableInfo('add');
    	$tableInfo->moduleName = 'user';
        $form = $tableInfo->generateForm($tableName,true);
        //var_dump($tableInfo->arrOptions);
        //exit;
        echo $form;
        //var_dump($form);exit;

    	/*$str .='<form class="form-horizontal" role="form"  method="post" action="__URL__/save/">';
    	foreach($allFields as $columnInfo){
    		if(!empty($selectedFields) && !in_array($columnInfo['column_name'], $selectedFields)) continue;
    		if(!I('hasId') && $columnInfo['COLUMN_KEY'] == "PRI") continue;



    		$str .= $this->createFormRow($columnInfo);
    		//$str .= '<option value="'.$columnInfo[$columnNameKey].'" >'.$columnInfo[$columnNameKey]."</option>\r\n";
    	}

    	$this->allRows = $str;
    	$r = $this->fetch("tpl_form");
    	foreach ($this->arrOptions as $k => $v){
    	       $this->assign('opt_'.$k,$v);
        }

    	$r = $this->fetch("",$r);

    	echo $r;*/
    	/* $str .= '<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default">保存</button>
    </div>
  </div>'; */
    	//$str .='</form>';
    	//echo $str;

    	echo "\n\n\n\n";
    	foreach ($this->options as $k => $v){
    		echo '"'.$tableName.'_'.$k.'"',"=>",$v,"\n\n";
    	}

    }

    //生成所有表的form,control,model
    function generateAll(){
        $prefix = C("DB_PREFIX");
        $tableNameList = I('tableName');
        $tableInfo = new TableInfo();
        if(empty($tableNameList)){
            $tableNameList = $tableInfo->getTableNameList();

        }

		foreach($tableNameList as $k => $tableInfo){


            $this->generateView($tableInfo);

            /*$className = ucfirst(str_replace($prefix,'',$tableName));
            $this->generateController($className);
            $this->generateModel($className);*/
		}

		echo "文件已经生成到: {$this->savePath}";



    }


    protected $savePath = "./data";

    /**
     * 生成controller
     * @param $className
     */
    function generateController($className){
        $tplPath = T('tpl_controller');
        $tpl = file_get_contents($tplPath);
        $tpl = str_replace('{$className}',$className,$tpl);
        $className = parse_name($className,1);
        $path = $this->savePath."/Controller";
        if (! file_exists ( $path ))  mkdir ( $path, 0777, true );
        file_put_contents("{$path}/{$className}Controller.class.php",$tpl);
    }

    /**
     * 生成model
     * @param $className
     */
    function generateModel($className){
        $tplPath = T('tpl_model');
        $tpl = file_get_contents($tplPath);
        $tpl = str_replace('{$className}',$className,$tpl);
        $className = parse_name($className,1);
        $path = $this->savePath."/Model";
        if (! file_exists ( $path ))  mkdir ( "$path", 0777, true );
        file_put_contents("{$path}/{$className}Model.class.php",$tpl);
    }

    /**
     * 生成view,添加表单，和列表文件,保存到data下
     * @param $tableName
     */
    function generateView($tableName){

        $tableInfo = new TableInfo('add');
        $tableInfoArray = $tableInfo->getTableInfoArray($tableName);
        $columnNameKey = strtoupper(TableInfo::getColumnNameKey());
        $str = '';


        $tableInfo->moduleName = 'user';
        $str = $tableInfo->generateForm($tableName,true);

        /*//生成添加表单
        $str .='<form class="form-horizontal" role="form"  method="post" action="__URL__/save/">';
        foreach($tableInfoArray as $columnInfo){
            //var_dump($columnInfo);exit;
            $str .= $this->createFormRow($columnInfo);
            //$str .= '<option value="'.$columnInfo[$columnNameKey].'" >'.$columnInfo[$columnNameKey]."</option>\r\n";
        }

        $this->allRows = $str;
        $str = $this->fetch("tpl_form");*/

        $prefix = C("DB_PREFIX");
        $className = ucfirst(str_replace($prefix,'',$tableName));
        $className = parse_name($className,1);
        $path = $this->savePath."/View/$className/";
        if (! file_exists ( $path ))  mkdir ( "$path", 0777, true );
        file_put_contents("$path/add.html",$str);


        //$tplPath = T('tpl_list');
        //$tpl = file_get_contents($tplPath);
        $tableInfo->page = 'list';
        $str = $tableInfo->generateLists($tableName);
        //$this->fields = $fields;
        $this->control = '__CONTROLLER__';
        $str = $this->fetch("tpl_list");
        file_put_contents("$path/index.html",$str);
    }

    function createListFields($tableInfoArray){
        $fields = [];
        foreach($tableInfoArray as $columnInfo){
            $commentInfo = $this->parserComment($columnInfo['column_comment']);
            $cnName = empty($commentInfo['name']) ? $columnInfo['column_name'] : $commentInfo['name'];
            $name = $columnInfo['column_name'];
            $fields[] = "$name:$cnName";
        }
        return implode(',',$fields);
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
        $tableInfo = new TableInfo();
		$tableName = I('tableName');
		if(is_array($tableName)){
		    $tableName = $tableName[count($tableName)-1];
        }
		$tableInfoArray = $tableInfo->getTableInfoArray($tableName);
		$columnNameKey = $tableInfo->getColumnNameKey();
//var_dump($tableInfoArray);exit;
		$str = '';
		foreach($tableInfoArray as $tableInfo){
			$str .= '<option value="'.$tableInfo[$columnNameKey].'" >'.$tableInfo[$columnNameKey]."</option>\r\n";
		}
		echo $str;
	}


    function index2($id=""){
    	$k = I('k');
    	$map = $where = [];
    	$mApi = M('lez_doc','','api');
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
    	$mApi = M('lez_doc','','api');
    	$this->detail = $mApi->find($id);


		$this->display();
	}

	function _before_save(){
		C('DEFAULT_FILTER',"");
		$_POST['method'] = strtoupper($_POST['method']);
		$_POST['update_time'] = time();
	}

	function show($id){
		$mApi = M('lez_doc','','api');
		$this->vo = $mApi->find($id);
		$this->display();
	}

    /**
     * 生成doc的模型字段注释
     * @param $tableName
     */
	function generateDoc($tableName){

        $tableInfo = new TableInfo('add',C('project_db_config'));
        $allFields = $tableInfo->getTableInfoArray($tableName);
        $allRows = [];
        foreach ($allFields as $columnInfo){

            $docInfo = [];
            $docInfo['type'] = $columnInfo['COLUMN_TYPE'];
            $docInfo['en_name'] = $columnInfo['COLUMN_NAME'];
            $commentInfo = $tableInfo->parseComment($columnInfo['COLUMN_COMMENT']);
            //$commentInfo = $tableInfo->parseComment("状态-select-禁用则不显示|1111|require|0:禁用,1:正常,2:审核中");
            $docInfo = array_merge($docInfo,$commentInfo);
            //var_dump($commentInfo);
            //var_dump($docInfo);
            //exit;
            $row = [];
            $row['en_name'] = $docInfo['en_name'];
            $row['name'] = $docInfo['name'];
            $row['checkType'] = $docInfo['checkType'];
            $row['htmlType'] = $docInfo['tips'];
            $row['tips'] = $docInfo['tips'];
            $row['type'] = $docInfo['type'];

            $strRules = "";
            foreach ($docInfo['arrRules']  as $k => $v){
                $strRules .= "{$v['type']} : {$v['msg']} : {$v['reg']} ".LF;
            }
            $row['arrRules'] = $strRules;

            $strOptions = "";
            foreach ($docInfo['options']  as $k => $v){
                $strOptions .= "{$k} : {$v} ".LF;
            }
            $row['options'] = $strOptions;
            $allRows[] = $row;
        }
        $this->list = $allRows;
        $this->display();
    }
	 
	 

}




