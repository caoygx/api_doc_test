<?php
namespace Home\Controller;
use Common\CommonController;

class DocController extends CommonController {
	protected $nodeId = [];	
	//protected $pre = "lez_";
	public  function _initialize(){
	}

    function index($id=""){
    	$k = I('k');
    	$project_id = I("project_id");

    	$map = ['project_id' =>$project_id];
        $where = [];
        $mApi = M('doc');
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
    	$mApi = M('doc');
    	$this->detail = $mApi->find($id);
        cookie( '_currentUrl_', __SELF__ );
		$this->display();
	}

    function import(){
        /*$
        */

        if(IS_GET){
            $this->display();
        }else{
            $raw = I('raw');
            $r = fiddlerPackageToDoc2($raw);
            //var_dump($r);exit;
            if(empty($r)) return;
            $r['project_id']=1;
            $m = M('Doc');

                $m->add($r);

        }
    }

    function _replacePublic($vo){
        //保密性
        $opt_status = C('options.doc_status');
        $this->status_selected = $vo['status'];
        $this->opt_status = $opt_status;
    }
	
	function _before_save(){
		C('DEFAULT_FILTER',"");
		$_POST['method'] = strtoupper($_POST['method']);
		$_POST['update_time'] = time();
		if(empty(I('module'))) $this->error('业务类型必须填写');
	}
	
	function show($id){
		$mApi = M('doc');
		$this->vo = $mApi->find($id);
		$this->display();
	}
	
	public $tplPhpMethod = '/**';
	
	function generatePhp($id){
	    $r = $this->m->find($id);
	    list($module,$control,$action) = explode('/', $r['url']);
	    $param = json_decode($r['param_json'],1);
	    $paramDescription = [];
	    foreach ($param as $k => &$v){
	        $temp = [];
	        $temp['type'] = gettype($v);
	        $temp['variableName'] = $k;
	        $paramDescription[] = $temp;
	    }
	    /* if(IS_AJAX){//ajax请求用于文档页面展示，需用html格式
	        $this->lf = "<br />";
	    }else{
	       $this->lf = "\n";
	    } */
	    $this->lf = "\n";
	    $this->action = $action;
	    $this->param = $paramDescription;
	    $this->description = $r['title'];
	    $r = $this->fetch("tpl_php");
	    echo $r;exit;
	}
	 
	function generateJava($id){
	    $r = $this->m->find($id);
	    list($module,$control,$action) = explode('/', $r['url']);
	    $param = json_decode($r['return_json'],1);
	    if(!empty($param['data']['list'])){
	        $param = $param['list'][0];
	    }else{
	        $param = $param['data'];
	    }
	    
	    $paramDescription = [];
	    foreach ($param as $k => &$v){
	        $temp = [];
	        $temp['type'] = empty(gettype($v)) ?: "string";
	        $temp['variableName'] = $k;
	        $paramDescription[] = $temp;
	    }
	    /* if(IS_AJAX){//ajax请求用于文档页面展示，需用html格式
	     $this->lf = "<br />";
	     }else{
	     $this->lf = "\n";
	     } */
	    $this->lf = "<br />";
	    $this->model = $control;
	    $this->param = $paramDescription;
	    $this->description = $r['title'];
	    $r = $this->fetch("tpl_java");
	    echo $r;exit;
	}
	
	function generateIos($id){
	    $r = $this->m->find($id);
	    list($module,$control,$action) = explode('/', $r['url']);
	    $param = json_decode($r['return_json'],1);
	    if(!empty($param['data']['list'])){
	        $param = $param['list'][0];
	    }else{
	        $param = $param['data'];
	    }
	    
	    $paramDescription = [];
	    foreach ($param as $k => &$v){
	        $temp = [];
	        $temp['type'] = empty(gettype($v)) ?: "string";
	        $temp['variableName'] = $k;
	        $paramDescription[] = $temp;
	    }
	    /* if(IS_AJAX){//ajax请求用于文档页面展示，需用html格式
	     $this->lf = "<br />";
	     }else{
	     $this->lf = "\n";
	     } */
	    $this->lf = "<br />";
	    $this->model = $control;
	    $this->param = $paramDescription;
	    $this->description = $r['title'];
	    $r = $this->fetch("tpl_ios");
	    echo $r;exit;
	}

	function getSqlStreamFromLog(){
	    $content = file_get_contents('sql.log');
	    $arrAllRequest = explode("---------------------------------------------------------------
",$content);
        foreach ($arrAllRequest as $k=>$v){
            var_dump($v);
        }
    }
	
	function showSqlFlow(){
		$id = I('id');
		$r = M('doc')->find($id);
		if(!empty($r['sql'])){
			$this->createSqlFlow($r['sql']);
		}
	}
	
	function createSqlFlow($sql){
		
$sql = explode("\n",$sql);
$sql = array_filter($sql);

//var_dump($sql);

$shapeConfig=[
    'select'=>'[shape=box,  fillcolor="#99CC66", style=filled ,label="{lable}" ,fontsize="16"]',
    'insert'=>'[shape=invtriangle,  fillcolor="#6666CC", style=filled ,label="{lable}",fontsize="16"]',
    'update'=>'[shape=polygon,sides=4,distortion=.7,  fillcolor="#FFCC00", style=filled ,label="{lable}",fontsize="16"]',
    'delete'=>'[shape=triangle,  fillcolor="#CC0033", style=filled ,label="{lable}",fontsize="16"]',
];

$sqlFlow = [];
foreach ($sql as $k => $v) {
    $operate = "";
    if(preg_match('/.*\s+join\s+(.*)/i',$v,$out)){
        $operate = "join";
		 if(preg_match('/select.+from\s+`?(\w+)`?join\s+(\w+)\s+/i',$v,$out)){
            $operate = "select";
			$tableName = $out[1].$out[2];
        }
		
    }else{
        if(preg_match('/select.+from\s+`?(\w+)`?/i',$v,$out)){
            $operate = "select";
        }elseif (preg_match('/INSERT INTO\s+`?(\w+)`?\s+/i',$v,$out)){
            $operate = "insert";
        }elseif(preg_match('/UPDATE\s+`?(\w+)`?\s+/i',$v,$out)){
            $operate = "update";
        }elseif(preg_match('/DELETE FROM\s+(\w+)\s+/i',$v,$out)){
            $operate = "delete";
        }
		$tableName = $out[1];
    }

    
    $nodeUniqueName = $tableName."_".$k;
    //$tableName .= "_".$k;
    $temp = [];
    $temp['objectName'] = $nodeUniqueName;
    $temp['property'] = $nodeUniqueName.' '.str_replace('{lable}',$operate."\n".$tableName,$shapeConfig[$operate]);
    $sqlFlow[] = $temp;

}

$dotConfig="";
$objectFlow = array_column($sqlFlow,'objectName');
$objectFlow = implode('->',$objectFlow);
$objectFlow .= ';';

$property = array_column($sqlFlow,'property');
$property = implode(";\n",$property);


/*$property="";
foreach ($sqlFlow as $k=>$v){
    $property .= $v['objectName'].' '.$v['property']."\n";
}*/

header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header ("Pragma: no-cache"); // HTTP/1.0
header ("Content-type: image/gif");
$somecontent='digraph example3 {
'.$objectFlow.'
 
'.$property.'
}';

//var_dump($somecontent);exit;
/*if (!$handle = fopen($filename, 'w')) {
    echo "cannot open $filename";
    exit;
}
if (fwrite($handle, $somecontent) === FALSE) {
    echo "cannot write to $filename";
    exit;
}
fclose($handle);*/




/*$somecontent = '
digraph example3 {
    Server1 -> Server2
Server2 -> Server3
Server3 -> Server1
 
Server1 [shape=box, label="Server1\nWeb Server", fillcolor="#ABACBA", style=filled]
Server2 [shape=triangle, label="Server2\nApp Server", fillcolor="#DDBCBC", style=filled]
Server3 [shape=circle, label="Server3\nDatabase Server", fillcolor="#FFAA22",style=filled]
}';*/

$filename="/tmp/digraph";
file_put_contents($filename,$somecontent);

//echo($somecontent);exit;

	passthru("dot -Tpng $filename");
//passthru("dot -Tpng $filename >dot.png");
	}
	 

}




