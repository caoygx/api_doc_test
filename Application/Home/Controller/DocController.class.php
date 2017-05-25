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
    	$map = $where = ['project_id' =>$project_id];
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
	 

}




