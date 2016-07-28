<?php

namespace Home\Controller;

use Think\Cache;
use Think\Crypt;

use Common\CommonController;

class AutotestController extends CommonController{
	protected $redis;
	protected $param;
	
	function _initialize(){
		header("Content-type: text/html; charset=utf-8");
		$this->redis = Cache::getInstance('redis');
		$this->param = array(); //请求参数
		$this->param['device_id'] = "355873053463106";
		$this->param['mobile'] = "13812341234";
		
		
	}

	public function _empty(){
		
	 	$url = I('url');
			$apis = findTestConfByUrl($url);
			if(!empty($apis) && is_array($apis))
		
			foreach ($apis as $api){
				if($api['filename']){
					$data = socket_test($api);
				}else{
					$data = request_by_curl($api);
				}
				//request_by_curl($api_config);
			}
		exit();
	 
		/* $api = C("test.".ACTION_NAME);
		//var_dump($api);exit('x');
		//$this->setParam($api);
		//var_dump($api);
		//exit;
		if($api['filename']){
			$data = socket_test($api);
		}else{
			$data = request_by_curl($api);
		} */
		
	}
	public function __get($name) {
		return isset($this->param[$name])?$this->param[$name]:null;
	}
	public function __isset($name) {
		return isset($this->param[$name]);
	}
	protected function success($msg){
		echo "<span style=\"color:#360;\">$msg</span>";
	}
	
	protected function error($msg){
		echo "<span style=\"color:#f00;\">$msg</span>";
	}
	
	//结果
	protected function ret($api,$data){
		$data = json_decode($data,1);
		//var_dump($data);exit('x');
		if($data['code'] == 1){
			$str .= $api['title'].LF;
			$str .= $api['url'].LF;
			$str .= "返回结果:".$data['msg'];
			//var_dump($data);
			$this->success($str);
		}else{
			$this->error($api['url']);
		}
		
	}
	
	
	function socket_test(){
		$r = socket_test("userloginv2");
		echo($r['content']);
	}
	
	protected function setParam(&$api){
		if(empty($api)){
			echo "api配置参数为能为空";
			return;
		}
		$param = array();
		foreach($api['data'] as $k => $v){
			if(is_string($k)){
				$param[$k] = $v;
			}else{
				$field = $v;
				if(empty($this->param[$field])){
					echo "参数：$field 缺失 ".LF;
				}
				$param[$field] = $this->param[$field];
			}
		}
		$param = http_build_query($param);
		$api['data'] = $param;
		//return $param;
	}
	
	function index(){
		
		$list = array();
		$list[] = array("id" => 1,"title" => "用户注册");
		$list[] = array("id" => 2,"title" => "用户登录");
		$list[] = array("id" => 2,"title" => "用户注册");
		
		$this->list = $list;
		$this->display();
		/* $m = M('api','',"yian");
		$c = $m->select();
		foreach ($c as $k => $v){
			echo '<a href="http://tools.daoxila.com/appcli/index.php/home/autotest/one?url='.$v['url'].'" target="_blank">',$v['title'],"</a> <br />";
		} */
		
	}
	
	function importConfig(){
		$c = C('test');
		$m = M('api','',"yian");
		foreach ($c as $k => &$v){
			is_array($v['data']) && $v['data'] = json_encode($v['data']);
			$m->add($v);
		}
	}
	
	
	function bat_api() {
		$st = gettimeofday(1);
        $id = I('id');
        $type = I('type');
		$m = M("test",'',"api");
        $where = array();
        $where["project"] = "api";
        $where["status"] = 1;
        !empty($id) && $where["id"] = $id;
        !empty($type) && $where["type"] = $type;
        $where['environment'] = I('env') == 'test' ? "test" : 'online';
        
		$r = $m->where($where)->select();
		//echo $m->getLastSql();
		$db_et = gettimeofday(1);
		//echo "============================test start ============================== \n";
		
		
		 foreach ($r as $k => $v){
			if(!empty($v) && is_array($v)){
				$data = request_by_curl_bat($v);
			}
		} 
		
		
		
		
		$et = gettimeofday(1);
		//echo "============================test end ============================== \n";
		//echo "A total of time: ".$et-$st."\n";
	
	}
	
	function testIdToRedis(){
		$m = M("test",'',"api");
		$where = array();
		$where["project"] = "api";
		$where["status"] = 1;
		$r = $m->where($where)->select();
		$redis = Cache::getInstance('redis');
		foreach ($r as $k => $v) {
			$redis->rPush("test_id",$v['id']);
		}
		
		
	}
	
	function multi(){
		$redis = Cache::getInstance('redis');
		pcntl_signal(SIGCHLD, SIG_IGN);
		while(1)//循环采用3个进程
		{
			
			//declare(ticks=1);
			$bWaitFlag= FALSE; // 是否等待进程结束
			//$bWaitFlag = TRUE; // 是否等待进程结束
			$intNum= 3; // 进程总数
			$pids= array(); // 进程PID数组
			for($i= 0; $i<$intNum; $i++)
			{
				$id = $redis->lPop("test_id");
				//var_dump($id);exit;
				$pids[$i] = pcntl_fork();// 产生子进程，而且从当前行之下开试运行代码，而且不继承父进程的数据信息
				/*if($pids[$i])//父进程
				 {
				 //echo $pids[$i]."parent"."$i -> " . time(). "\n";
				 }
				*/
				if($pids[$i] == -1)
				{
					echo"couldn't fork". "\n";
				}
				elseif(!$pids[$i])
				{
					sleep(1);
					//echo "id:".$id."\n";
					
					
					//echo"\n"."第".$i."个进程 -> ". time(). "\n";
					$file = __FILE__;
					passthru ("php $file  /home/autotest/bat_api/id/$id");
					//$data = request_by_curl_bat($v);
					exit(0);//子进程要exit否则会进行递归多进程，父进程不要exit否则终止多进程
				}
				if($bWaitFlag)
				{
					pcntl_waitpid($pids[$i], $status, WUNTRACED);echo"wait $i -> ". time() . "\n";
				}
			}
			sleep(1);
		}
	}
	
	
	
	function random_mobile(){
		$pre = array(130,131,132,133,134,135,136,137,138,139,
					 150,151,152,153,155,156,157,158,159,
					 176,177,178,
					 180,181,182,183,184,185,186,187,188,189,
					 145,147
		);
		$rand = mt_rand(0, 99999999);
		$rand=sprintf("%08d", $rand);
		
		$pre_key = array_rand($pre);
		$pre = $pre[$pre_key];
		
		return $pre.$rand;
	}
	
	
	
	function debug(){
		print_stack_trace("xxxxxxxx");
	}
	
	
}

/*
 * 打印调用栈的信息
 * @param string $msg
 * 需要打印出来的消息
 * @param function $log_handler
 * 处理日志的函数，如果为null，则调用print函数打印日志
 * @param string $endline
 * 行结束符，如果显示在网页上，可以设置为'<br/>'
 * @param bool $exit
 * 打完日志后是否退出程序
 */
function print_stack_trace($msg, $log_handler = null, $endline = "\n", $exit=false){
	$trace = debug_backtrace();
	$num = 0;
	$ans = 'message:'.$msg.$endline.'stact trace back :'.$endline;
	foreach($trace as $line){
		
		$ans .= '#'.$num.' '.$line['file'].'['.$line['line'].'] ';
		
		$args = array();
		$args = implode(',', $line['args']);
		//var_dump($line);continue;
		if($line['type'] == '->' || $line['type'] == '::'){
			$ans .= $line['class'].$line['type'].$line['function'].'()';
		}else{
			$ans .= $line['function'].'()';
		}
		if(!empty($line['args'])){
			$ans .= $endline.'parameters:'.$endline.print_r($line['args'], true);
		}
		if(!empty($line['object'])){
			//$oReflectionClass = new \ReflectionClass($line['object']);
			//var_dump($oReflectionClass->name);
			$ans .= $endline.'object:'.$endline.print_r($line['object'], true);
		}
		$ans .= $endline;
		$num++;
	}
	if($log_handler != null && function_exists($log_handler)){
		$log_handler($ans);
	}else{
		print $ans;
	}
	if($exit){
		exit(1);
	}
}

	
