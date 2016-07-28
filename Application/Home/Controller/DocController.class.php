<?php
namespace Home\Controller;
use Common\CommonController;

class DocController extends CommonController {
	protected $nodeId = [];	
	protected $pre = "rrbrr_";
	public  function _initialize(){
		C('DB_PREFIX',"");
	}
    function index($id=""){
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




