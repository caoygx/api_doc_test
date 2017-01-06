<?php
namespace Home\Controller;
use Common\CommonController;

class DocController extends CommonController {
	protected $nodeId = [];	
	//protected $pre = "lez_";
	public  function _initialize(){
		C('DB_PREFIX',"lez_");
	}
    function index($id=""){
    	$k = I('k');
    	$map = $where = [];
    	$mApi = M('doc','','api');
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
    	$mApi = M('doc','','api');
    	$this->detail = $mApi->find($id);
        cookie( '_currentUrl_', __SELF__ );
		$this->display();
	}
	
	function _before_save(){
		C('DEFAULT_FILTER',"");
		$_POST['method'] = strtoupper($_POST['method']);
		$_POST['update_time'] = time();
	}
	
	function show($id){
		$mApi = M('doc','','api');
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




