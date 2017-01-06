<?php
/**
 * Created by PhpStorm.
 * User: 许诺峰
 * Date: 15-6-4
 * Time: 上午11:51
 */

namespace Home\Controller;

use Common\CommonController;
use Common\Cookie;

class TestController extends CommonController{
		function _initialize(){
			parent::_initialize();
			C('DB_PREFIX',"lez_");
			$this->m = M('doc', '', 'api');
		}
		
		function _before_index(){
			if(empty(I('environment'))){
				$_REQUEST['environment'] = 'test';
			}
		}
	
        public function insert() {
            $data = $this->index();
            $test = M('test', '', 'api');
            $r = $test->addAll($data);
        }
        
       
        public function _filter(&$map){
        	if($_GET['title']){
        		$title = I('get.title');
        		$map['title'] = array('like',"%$title%");
        	}
        	if($_GET['url']){
        		$url = I('get.url');
        		$map['url'] = array('like',"%$url%");
        	}
        	if($_GET['status']){
        		$map['status'] = I('get.status');
        	}else{
        		$map['status'] = 1;
        	}
        }
        
    
        
        public function test(){
        	header("Content-type:text/html;charset=utf-8");
        	$id = I('id');
        	$api = $this->m->find($id);
        	//echo $this->m->getLastSql();exit;
        	//var_dump($this->m);exit;
        	if(!empty($api) && is_array($api)){
        		$data = request_by_curl($api);
        		
        		//$data = request_by_curl_parameter_combination($api);
        		
        		//request_by_curl($api_config);
        	}
        	exit('没有此记录'); 
        }
        
        function copy(){
        	$id = I('id');
        	$data = $this->m->find($id);
        	unset($data['id']);
        	$id = $this->m->add($data);
        	$url =  __CONTROLLER__."/edit?id=$id";
        	redirect($url);
        	//redirect( 'edit?id=668');
        }
        
        public function more(){
        	
        	$url = I('url');
        	$where['url'] = array('like',"%$url%");
        	$more = $api = $this->m->where($where)->select();
        	$this->ajaxReturn($more);
        }
        
        function _before_save(){
        	C('DEFAULT_FILTER',"");
        	$rules = array(
        		array('url','require','请输入URl'),
         	);
        	$this->m->validate($rules);
        }
        
        
        public function importHttp() {
        	if (! $_POST) {
        		$this->display ();
        	} else {
        		$text = I ( 'post.text' ,'','htmlspecialchars_decode');
        		$text = urldecode($text);
        		if (stristr ( $text, "\r\n\r\n" )) {
        			$arr = explode ( "\r\n\r\n", $text );
        			$arr_data = $arr [1];
        			$arr = explode ( "\r\n", $arr [0] );
        		} else {
        			$arr = explode ( "\r\n", $text );
        		}
        
        		if (! empty ( $arr [0] )) {
        			$first_line = explode ( " ", $arr [0] );
        			$method = $first_line [0];
        			$url = $first_line [1];
        			$url_api = URL_API;	
        			$url = str_replace ( "https://{$url_api}", '', $url );
        			$url = explode ( '?', $url );
        			$check ['url'] = $data ['url'] = $url [0];

        				
        			if ($method == 'POST') {
        				$data ['get'] = 0;
        				$params = explode ( "&", $arr_data );
        
        				$json = array ();
        				foreach ( $params as $param ) {
        					$tmp = explode ( '=', $param );
        					$json [$tmp [0]] = $tmp [1];
        				}
        				$data ['data'] = json_encode ( $json );
        			} else {
        				$data ['get'] = 1;
        				$params = explode ( "&", $url [1] );
        				$json = array ();
        				foreach ( $params as $param ) {
        					$tmp = explode ( '=', $param );
        					$json [$tmp [0]] = $tmp [1];
        				}
        				$data ['data'] = json_encode ( $json );
        			}
        			$r = $this->m->add ( $data );
        			if ($r!==false) { //保存成功
        				//$this->assign ( 'jumpUrl', cookie( '_currentUrl_' ) );
        				$this->success ('保存成功!',cookie( '_currentUrl_' ),array("id" => $r,"keyxx" => "valuexx"));
        			} else {
        				//失败提示
        				$this->error ('保存失败!');
        			}
        		}
        	}
        }
        
        public function hosts() {
			if (! $_POST  ) {
				return false;
				exit ();
			}
			if(!filter_var(I('post.host'), FILTER_VALIDATE_IP)){
				echo -1;exit();
			}
			$host = I ( 'post.host' );
			$file = file_get_contents ( '/etc/hosts' );
			$url_api = URL_API;
			$ip = gethostbyname ($url_api );
			preg_match ( "/{$ip} .*{$url_api}/", $file, $txt );
			if ($txt) {
				$newTxt = str_replace ( $ip, $host, $txt [0] );
				$file = str_replace ( $txt [0], $newTxt, $file );
				file_put_contents ('/etc/hosts', $file );
			}
		}
	
	
        
  
	
} 