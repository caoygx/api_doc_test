<?php
function findTestConfByUrl($url){
	$testConf = C("test");
	$ret = array();
	foreach($testConf as $k => $v) {
		if($v['url'] == $url) {
			$ret[] = $v;
		}
	}
	return $ret;
	// var_dump($testConf);
}

// curl 访问接口
function request_by_curl($api, $host = "",$show_form = true){
	empty($host) && $host = URL_API;
	echo $api['title'], ":", $api['url'], LF;
	if(substr($api['url'], 0,4) != "http"){
		$url = $host . $api['url'];
	}else{
		$url = $api['url'];
	}
	
	if(!is_array($api['data'])){
		$api['data'] = json_decode($api['param_json']);
	}
	$method = $api['method'];
	if($show_form){
	echo '<form id="form1" name="form1" method="' . $method . '" enctype="multipart/form-data" action="' . $url . '" target="_blank">';
	echo "<table>";
	foreach($api['data'] as $k => $v) {
		if(is_array($v)) {
			if($v['type'] == "file")
				echo "<tr><td>$k : </td><td><input type='file' name = '$k' value='{$v['value']}'></td> </tr>";
		} else {
			echo "<tr><td>$k : </td><td><input type='text' name = '$k' value='$v'></td> </tr>";
		}
	}
	echo "</table>";
	echo '<input type="submit" name="button"   value="提交" id="send" /><br /></form>';
	$errors = array();
	$ret = curl_get_content($url, $api['data'], $method);
	echo <<<HTML
	
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<div>
<a onclick="show(this)" style="cursor:pointer; color:blue;" >显示隐藏返回结果</a>
<div style="display:none; color:#c96;">$ret</div>
</div>
<script>
function show(self){
	$(self).parent().children("div").toggle();
	
}
</script>
HTML;
	
	}
	$ret = json_decode($ret, true);
	// var_dump($ret);exit('xx');
	if(! empty($api['return_json'])) {
		$return_json = $api['return_json'];
		// var_dump($return_json);
		if(empty($ret)) {
			echo '<span style="color:#f00">无数据</span>', LF;
		}
		if(! is_array($return_json)) {
			$return_json = json_decode($return_json, true);
		}

		if(empty($return_json)) {
			echo "返回格式解析结果为空" . LF;
			return;
		}
		// var_dump($return_json);
		check_ecursive($return_json, $ret);
		// array_walk_recursive($return_json,"check");
		/*
		 * foreach ($return_json as $k => $v){ if(empty($ret[$k])){ $errors[$k] = "is null"; } }
		 */
	} else {
		echo "返回格式不能为空 <hr />" . LF;
		return $ret;
	}
	echo "测试完毕 <hr /><br /><br />";
	return $ret;
}




// curl 组合参数访问接口
function request_by_curl_parameter_combination($api, $host = "",$show_form = true){
	empty($host) && $host = URL_API;
	echo $api['title'], ":", $api['url'], LF;
	if(substr($api['url'], 0,4) != "http"){
		$url = $host . $api['url'];
	}else{
		$url = $api['url'];
	}
	if(! isset($api['get'])) {
		$api['get'] = 1;
	}
	if(!is_array($api['data'])){
		$api['data'] = json_decode($api['data'],true);
	}
	
	$method = $api['get'] ? "get" : "post";
	
	$parameter = $api['data'];
	$arr = array_keys($api['data']);
	$n = count($arr);
	$allCombination = array();
	for($i = 1; $i <= $n; $i ++) {
		$t = getCombinationToString($arr, $i);
		$allCombination = array_merge($allCombination, $t);
	}
	
	
	//ob_end_flush();
	foreach ($allCombination as $k => $v ){
		$parms = explode(',', $v);
		$newParms = array();
		foreach ($parms as $k => $v){
			$newParms[$v] = $parameter[$v];
		}
		$parms = $newParms;
		$ret = curl_get_content($url, $parms, $api['get']);
		 $ret = json_decode($ret,true);
		if($ret['code'] != 1){
			
			$text = "para : ".var_export($parms,1).LF."ret : ".var_export($ret,1);
			echo_color($text, "FAILURE", 1);
			echo LF,LF;
			
		}else{ 
			//echo_color("成功".LF,"GREEN");
			
			$text = "para : ".var_export($parms,1).LF."ret : ".var_export($ret,1);
			echo_color($text, "SUCCESS", 1);
			echo LF,LF;
	
		}
		
		/* if($k > 10){
			exit('x');
		} */	
	}
	$ret = json_decode($ret, true);
	// var_dump($ret);exit('xx');
	if(! empty($api['return_json'])) {
		$return_json = $api['return_json'];
		// var_dump($return_json);
		if(empty($ret)) {
			echo '<span style="color:#f00">无数据</span>', LF;
		}
		if(! is_array($return_json)) {
			$return_json = json_decode($return_json, true);
		}

		if(empty($return_json)) {
			echo "返回格式解析结果为空" . LF;
			return;
		}
		// var_dump($return_json);
		check_ecursive($return_json, $ret);
		// array_walk_recursive($return_json,"check");
		/*
		 * foreach ($return_json as $k => $v){ if(empty($ret[$k])){ $errors[$k] = "is null"; } }
		*/
	} else {
		echo "返回格式不能为空 <hr />" . LF;
		return $ret;
	}
	echo "测试完毕 <hr /><br /><br />";
	return $ret;
}



// curl 组合参数访问接口
function request_by_curl_bat($api, $host = "",$show_form = true){
	empty($host) && $host = URL_API;
	if(substr($api['url'], 0,4) != "http"){
		$url = $host . $api['url'];
	}else{
		$url = $api['url'];
	}
	if(! isset($api['method'])) {
		$api['method'] = 'get';
	}
	if(!is_array($api['param_json'])){
		$api['param_json'] = json_decode($api['param_json'],true);
	}
	$method = $api['get'] ? "GET" : "POST";
	$parms = $api['param_json'];
    echo LF;

    $text = $api['title'].":".$api['url'];
    echo_color("url: ".$text, "MAGENTA", 0);echo LF;
    echo_color("params: ".json_encode($parms,JSON_UNESCAPED_UNICODE), "LIGHT_BLUE", 0); echo LF;


	$id = $api['id'];
	$ret = curl_get_content($url, $parms, $api['method']);
	$ret = json_decode($ret,true);
    if(json_last_error() != JSON_ERROR_NONE){
        $text = '返回的json 解析错误';
        echo_color($text, "FAILURE", 1);
        echo LF,LF;
    }
	$showDetails = false;
	if($showDetails){
		echo $api['title'], ":", $api['url'], LF;
		if($ret['code'] != 1){
			$text = "para : ".var_export($parms,1).LF."ret : ".var_export($ret,1);
			echo_color($text, "FAILURE", 1);
			echo LF,LF;
		}else{
			//echo_color("成功".LF,"GREEN");
			$text = "para : ".var_export($parms,1).LF."ret : ".var_export($ret,1);
			echo_color($text, "SUCCESS", 1);
			echo LF,LF;
		}
	}else{

        $text = "";
		if(!empty($ret['http_code'])){
			echo_color("[http_status:{$ret['http_code']} $method] ".$text."    ".$ret['data'], "RED");
		}else{
			if($ret['code'] != 1){}
			echo_color("[http_status:200 $method  id:$id] ".$text, "GREEN");
		}
		echo LF;
	}
	
	$return_json = $api['return_json'];
	//var_dump($return_json);exit;
	$return_json = json_decode($return_json,1);
	switch (json_last_error()) {
		case JSON_ERROR_NONE:
			//echo '没有错误发生';
			break;
		case JSON_ERROR_DEPTH:
			echo '    return_json 到达了最大堆栈深度'.LF;
			break;
		case JSON_ERROR_STATE_MISMATCH:
			echo '    return_json 无效或异常的 JSON'.LF;
			break;
		case JSON_ERROR_CTRL_CHAR:
			echo '    return_json 控制字符错误，可能是编码不对'.LF;
			break;
		case JSON_ERROR_SYNTAX:
			echo '    return_json 语法错误'.LF;
			break;
		case JSON_ERROR_UTF8:
			echo '    return_json 异常的 UTF-8 字符，也许是因为不正确的编码。'.LF;
			break;
		default:
			echo '    return_json 未知错误'.LF;
			break;
	}
	$ret_data = $ret;
	if(!empty($return_json) && is_array($return_json) && !empty($ret_data) && is_array($ret_data)){
		
		check_ecursive_cli($return_json, $ret_data);
	}

    echo '----------------------------------------------------------------------------------------------------------------';
    echo LF;echo LF;
	return $ret;
}

function analysis_result($ret){
	$ret = '{"code":1}';
	
	$ret = json_decode($ret, true);
	 
	$return_json = '{
								  "count": 333,
								  "items": [
								    {
								      "id": "2143",

								      "name": "维多利亚"
								      
								    }
								  ]
								}';
	$return_json = json_decode($return_json,true);
	 check_ecursive_cli($return_json, $ret);
	
}


function check($item, $key){
	echo "$key holds $item\n";
}
global $errors;
$errors = array();
function check_ecursive($return_json, $ret, $prve_key = ''){
	global $errors;
	
	foreach($return_json as $k => $v) {
		if(is_array($v)) {
			// var_dump($v);
			/*
			 * if(empty($prve_key)){ $prve_key = $k; }else{ $prve_key = $prve_key.".".$k; }
			 */
			// $prve_key = $prve_key.".".$k;
			check_ecursive($v, $ret[$k], $prve_key . "." . $k);
		} else {
			
			// $prve_key = str_replace($k, '', $prve_key);
			/*
			 * var_dump($ret); echo "<hr />";
			 */
			if(empty($ret[$k])) {
				echo '<span style="color:#f00">';
				echo "$prve_key.{$k} : ";
				var_dump($ret[$k]);
				echo "</span>";
				if($v === "not null") {
					// echo $v;
					echo "$k 不能为空";
				}
				echo LF;
			}
			/*
			 * if(!$v){ $errors[$k] = $ret[$k]; }
			 */
		}
		// var_dump($ret[$v]);
	}
	// $prve_key = substr(strrchr($prve_key, '.'), 1);
	/*
	 * var_dump($errors); foreach ($errors as $k => $v){ echo '<span style="color:#f00">',$k,":",$v,"</span>"; echo LF; }
	 */
}

function check_ecursive_cli($return_json, $ret, $prve_key = ''){
	global $errors,$warning;
	foreach($return_json as $k => $v) {
		if(is_array($v)) {
			check_ecursive_cli($v, $ret[$k], $prve_key . "." . $k);
		} else {
			if(!array_key_exists($k,$ret)  ) {
				echo_color("    $prve_key.{$k} : ", "BROWN");
				echo_color( var_export($ret[$k],1), "RED");
				echo LF;
				if($v === "not null") {
					echo "$k 不能为空";
				}
				echo 'error';
                $errors = true;
			}elseif(empty($ret[$k])){
				echo 'waring';
                $warning = true;
				echo_color("    $prve_key.{$k} : ", "BROWN");
			}
		}
	}
}

function encrypt($uid){
	$key = 'zb8964116sjts3dhe156ydsx';
	$iv = '1d3w6g8x';
	$pad = mcrypt_get_block_size("tripledes", "cbc") - (strlen($uid) % mcrypt_get_block_size("tripledes", "cbc"));
	$padded = $uid . str_repeat(chr($pad), $pad);
	$uid = @base64_encode(mcrypt_encrypt("tripledes", $key, $padded, "cbc", $iv));
	// $id = str_replace("+",chr(32),$uid);
	$id = str_replace(chr(32), "+", $uid);
	return $id;
}


/**
 * SOCKET扩展函数
 * 
 * @copyright (c) 2013
 * @author Qiufeng <fengdingbo@gmail.com>
 * @link http://www.fengdingbo.com
 * @version 1.0
 *         
 */

/**
 * Post Request
 *
 * @param string $url        	
 * @param array $data        	
 * @param string $referer        	
 * @return array
 *
 */
function socket_post($url, $data, $referer = ''){
	if(! is_array($data)) {
		return;
	}
	
	$data = http_build_query($data);
	$url = parse_url($url);
	
	if(! isset($url['scheme']) || $url['scheme'] != 'http') {
		die('Error: Only HTTP request are supported !');
	}
	
	$host = $url['host'];
	$path = isset($url['path']) ? $url['path'] . '?' . $url['query'] : '/';
	
	// open a socket connection on port 80 - timeout: 30 sec
	$fp = fsockopen($host, 80, $errno, $errstr, 30);
	
	if($fp) {
		// send the request headers:
		$length = strlen($data);
		$POST = <<<HEADER
POST {$path} HTTP/1.1
Host: {$host}
Accept: text/plain, text/html
Accept-Language: zh-CN,zh;q=0.8
Content-Type: application/x-www-form-urlencodem
Cookie: token=value; pub_cookietime=2592000; pub_sauth1=value; pub_sauth2=value
User-Agent: DaoxilaApp/2.0.0 (iPhone; iOS 8.1.2; Scale/2.00)
Content-Length: {$length}
Pragma: no-cache
Cache-Control: no-cache
Connection: close
Cookie: baJf_2132_forum_lastvisit=D_36_1423124866D_2_1423125031; baJf_2132_lastact=1423125486%09index.php%09register; baJf_2132_lastvisit=1423112308; baJf_2132_saltkey=g8d1xdd2; baJf_2132_sid=lscGh3; baJf_2132_st_p=0%7C1423115953%7C65172f6cb84ba1c9fba80eebe871fa65; baJf_2132_st_t=0%7C1423125031%7Cd1ca3eca1f07f48c87c3be3d7b29e257; baJf_2132_viewid=tid_6475; baJf_2132_visitedfid=2D36

{$data}
HEADER;
		// echo $POST;exit('x11111');
		fwrite($fp, $POST);
		$result = '';
		while(! feof($fp)) {
			// receive the results of the request
			$result .= fread($fp, 512);
		}
	} else {
		return array(
				'status' => 'error',
				'error' => "$errstr ($errno)" 
		);
	}
	
	// close the socket connection:
	fclose($fp);
	
	// split the result header from the content
	$result = explode("\r\n\r\n", $result, 2);
	
	// var_dump($host);
	
	// var_dump($path);
	// var_dump($result);exit('x');
	// return as structured array:
	return array(
			'status' => 'ok',
			'header' => isset($result[0]) ? $result[0] : '',
			'content' => isset($result[1]) ? $result[1] : '' 
	);
}

/*
 * print_r(socket_post('http://bbs.daoxila.com/api/dxlapp/index.php?module=register&version=4&is_app=1', array( "password" => '000000', 'password2' => '000000', 'userid' => 'WWsaW3WARvg', 'username' => 'Aaaaaa' )));
 */
function post_request($url, $data, $referer = ''){
	$data = http_build_query($data);
	$url = parse_url($url);
	if($url['scheme'] != 'http') {
		die('Error: Only HTTP request are supported !');
	}
	$host = $url['host'];
	$path = $url['path'];
	$fp = fsockopen($host, 80, $errno, $errstr, 30);
	if($fp) {
		$length = strlen($data);
		$POST = <<<HEADER
POST /api/dxlapp/index.php?module=register&version=4&is_app=1 HTTP/1.1
Host: $host
Accept: text/plain, text/html
Accept-Language: zh-CN,zh;q=0.8
Content-Type: application/x-www-form-urlencodem
Cookie: token=value; pub_cookietime=2592000; pub_sauth1=value; pub_sauth2=value
User-Agent: DaoxilaApp/2.0.0 (iPhone; iOS 8.1.2; Scale/2.00)
Content-Length: {$length}
Pragma: no-cache
Cache-Control: no-cache
Connection: close
Cookie: baJf_2132_forum_lastvisit=D_36_1423124866D_2_1423125031; baJf_2132_lastact=1423125486%09index.php%09register; baJf_2132_lastvisit=1423112308; baJf_2132_saltkey=g8d1xdd2; baJf_2132_sid=lscGh3; baJf_2132_st_p=0%7C1423115953%7C65172f6cb84ba1c9fba80eebe871fa65; baJf_2132_st_t=0%7C1423125031%7Cd1ca3eca1f07f48c87c3be3d7b29e257; baJf_2132_viewid=tid_6475; baJf_2132_visitedfid=2D36

$data
HEADER;
		/*
		 * POST {$path} HTTP/1.1 Host: {$host} Cookie: baJf_2132_forum_lastvisit=D_36_1423124866D_2_1423125031; baJf_2132_lastact=1423125486%09index.php%09register; baJf_2132_lastvisit=1423112308; baJf_2132_saltkey=g8d1xdd2; baJf_2132_sid=lscGh3; baJf_2132_st_p=0%7C1423115953%7C65172f6cb84ba1c9fba80eebe871fa65; baJf_2132_st_t=0%7C1423125031%7Cd1ca3eca1f07f48c87c3be3d7b29e257; baJf_2132_viewid=tid_6475; baJf_2132_visitedfid=2D36
		 */
			/*fputs($fp, "POST /api/dxlapp/index.php?module=register&version=4&is_app=1 HTTP/1.1\r\n");
			fputs($fp, "Host: $host\r\n");
			 if ($referer != '')
				fputs($fp, "Referer: $referer\r\n"); 
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ". strlen($data) ."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $data);*/
			fputs($fp, $POST);
		$result = '';
		while(! feof($fp)) {
			$result .= fgets($fp, 128);
		}
	} else {
		return array(
				'status' => 'err',
				'error' => "$errstr ($errno)" 
		);
	}
	
	// close the socket connection:
	fclose($fp);
	
	// split the result header from the content
	$result = explode("\r\n\r\n", $result, 2);
	
	$header = isset($result[0]) ? $result[0] : '';
	$content = isset($result[1]) ? $result[1] : '';
	
	// return as structured array:
	return array(
			'status' => 'ok',
			'header' => $header,
			'content' => $content 
	);
}
function _dfsockopen($url, $post = '', $cookie = '', $option = array()){
	extract($option, EXTR_SKIP);
	! isset($limit) && $limit = 0;
	! isset($bysocket) && $bysocket = FALSE;
	! isset($ip) && $ip = '';
	! isset($timeout) && $timeout = 15;
	! isset($block) && $block = TRUE;
	! isset($encodetype) && $encodetype = 'URLENCODE----';
	! isset($allowcurl) && $allowcurl = false;
	! isset($position) && $position = false;
	! isset($files) && $files = array();
	
	$return = '';
	$matches = parse_url($url);
	$scheme = $matches['scheme'];
	$host = $matches['host'];
	$path = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
	$port = ! empty($matches['port']) ? $matches['port'] : ($scheme == 'http' ? '80' : '');
	$boundary = $encodetype == 'URLENCODE' ? '' : random(40);
	
	if($post) {
		if(! is_array($post)) {
			parse_str($post, $post);
		}
		_format_postkey($post, $postnew);
		$post = $postnew;
	}
	if(function_exists('curl_init') && function_exists('curl_exec') && $allowcurl) {
		$ch = curl_init();
		$httpheader = array();
		if($ip) {
			$httpheader[] = "Host: " . $host;
		}
		if($httpheader) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
		}
		curl_setopt($ch, CURLOPT_URL, $scheme . '://' . ($ip ? $ip : $host) . ($port ? ':' . $port : '') . $path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		if($post) {
			curl_setopt($ch, CURLOPT_POST, 1);
			if($encodetype == 'URLENCODE') {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			} else {
				foreach($post as $k => $v) {
					if(isset($files[$k])) {
						$post[$k] = '@' . $files[$k];
					}
				}
				foreach($files as $k => $file) {
					if(! isset($post[$k]) && file_exists($file)) {
						$post[$k] = '@' . $file;
					}
				}
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			}
		}
		if($cookie) {
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$data = curl_exec($ch);
		$status = curl_getinfo($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		if($errno || $status['http_code'] != 200) {
			return;
		} else {
			$GLOBALS['filesockheader'] = substr($data, 0, $status['header_size']);
			$data = substr($data, $status['header_size']);
			return ! $limit ? $data : substr($data, 0, $limit);
		}
	}
	if($post) {
		if($encodetype == 'URLENCODE') {
			$data = http_build_query($post);
		} else {
			$data = '';
			foreach($post as $k => $v) {
				$data .= "--$boundary\r\n";
				$data .= 'Content-Disposition: form-data; name="' . $k . '"' . (isset($files[$k]) ? '; filename="' . basename($files[$k]) . '"; Content-Type: application/octet-stream' : '') . "\r\n\r\n";
				$data .= $v . "\r\n";
			}
			foreach($files as $k => $file) {
				if(! isset($post[$k]) && file_exists($file)) {
					if($fp = @fopen($file, 'r')) {
						$v = fread($fp, filesize($file));
						fclose($fp);
						$data .= "--$boundary\r\n";
						$data .= 'Content-Disposition: form-data; name="' . $k . '"; filename="' . basename($file) . '"; Content-Type: application/octet-stream' . "\r\n\r\n";
						$data .= $v . "\r\n";
					}
				}
			}
			$data .= "--$boundary\r\n";
		}
		$out = "POST $path HTTP/1.0\r\n";
		$header = "Accept: */*\r\n";
		$header .= "Accept-Language: zh-cn\r\n";
		$header .= $encodetype == 'URLENCODE' ? "Content-Type: application/x-www-form-urlencoded\r\n" : "Content-Type: multipart/form-data; boundary=$boundary\r\n";
		$header .= 'Content-Length: ' . strlen($data) . "\r\n";
		$header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$header .= "Host: $host:$port\r\n";
		$header .= "Connection: Close\r\n";
		$header .= "Cache-Control: no-cache\r\n";
		$header .= "Cookie: $cookie\r\n\r\n";
		$out .= $header;
		$out .= $data;
	} else {
		$out = "GET $path HTTP/1.0\r\n";
		$header = "Accept: */*\r\n";
		$header .= "Accept-Language: zh-cn\r\n";
		$header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$header .= "Host: $host:$port\r\n";
		$header .= "Connection: Close\r\n";
		$header .= "Cookie: $cookie\r\n\r\n";
		$out .= $header;
	}
	echo $out;
	$fpflag = 0;
	if(! $fp = fsocketopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout)) {
		$context = array(
				'http' => array(
						'method' => $post ? 'POST' : 'GET',
						'header' => $header,
						'content' => $post,
						'timeout' => $timeout 
				) 
		);
		$context = stream_context_create($context);
		$fp = @fopen($scheme . '://' . ($ip ? $ip : $host) . ':' . $port . $path, 'b', false, $context);
		$fpflag = 1;
	}
	
	if(! $fp) {
		return '';
	} else {
		stream_set_blocking($fp, $block);
		stream_set_timeout($fp, $timeout);
		@fwrite($fp, $out);
		$status = stream_get_meta_data($fp);
		if(! $status['timed_out']) {
			while(! feof($fp) && ! $fpflag) {
				$header = @fgets($fp);
				$headers .= $header;
				if($header && ($header == "\r\n" || $header == "\n")) {
					break;
				}
			}
			$GLOBALS['filesockheader'] = $headers;
			
			if($position) {
				for($i = 0; $i < $position; $i ++) {
					$char = fgetc($fp);
					if($char == "\n" && $oldchar != "\r") {
						$i ++;
					}
					$oldchar = $char;
				}
			}
			
			if($limit) {
				$return = stream_get_contents($fp, $limit);
			} else {
				$return = stream_get_contents($fp);
			}
		}
		@fclose($fp);
		return $return;
	}
}
function _format_postkey($post, &$result, $key = ''){
	foreach($post as $k => $v) {
		$_k = $key ? $key . '[' . $k . ']' : $k;
		if(is_array($v)) {
			_format_postkey($v, $result, $_k);
		} else {
			$result[$_k] = $v;
		}
	}
}
function fsocketopen($hostname, $port = 80, &$errno, &$errstr, $timeout = 15){
	$fp = '';
	if(function_exists('fsockopen')) {
		$fp = @fsockopen($hostname, $port, $errno, $errstr, $timeout);
	} elseif(function_exists('pfsockopen')) {
		$fp = @pfsockopen($hostname, $port, $errno, $errstr, $timeout);
	} elseif(function_exists('stream_socket_client')) {
		$fp = @stream_socket_client($hostname . ':' . $port, $errno, $errstr, $timeout);
	}
	return $fp;
}
function random($length, $numeric = 0){
	$seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
	if($numeric) {
		$hash = '';
	} else {
		$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
		$length --;
	}
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i ++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}
function socket_test($api){
	echo $api['title'], ":", $api['url'], LF;
	$url = $host . $api['url'];
	if(! isset($api['get'])) {
		$api['get'] = 1;
	}
	
	$method = $api['get'] ? "get" : "post";
	/*
	 * foreach ($api['data'] as $k => $v){ <tr><td>$k : </td><td><input type='text' name = '$k' value='$v'></td> </tr> }
	 */
	
/* echo <<<HTML 
	<form id="form1" name="form1" method="$method" action="$url" target="_blank">;
	<table>
	
	</table>
	<input type="submit" name="button"   value="提交" id="send" /><br /></form>
HTML;
 */	
	$errors = array();
	$filename = str_replace('/', '_', $api['url']);
	$filename = substr($filename, 1);
	
	$result = socket_file($filename);
	
	$ret = $result;
	// var_dump($ret);exit;
	echo <<<HTML

<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<div>
<a onclick="show(this)" style="cursor:pointer; color:blue;" >显示隐藏返回结果</a>
<div style="display:none; color:#c96;">$ret</div>
</div>
<script>
function show(self){
	$(self).parent().children("div").toggle();

}
</script>
HTML;
	
	$ret = json_decode($ret, true);
	// var_dump($ret);exit('xx');
	if(! empty($api['return_json'])) {
		$return_json = $api['return_json'];
		// var_dump($return_json);
		if(empty($ret)) {
			echo '<span style="color:#f00">无数据</span>', LF;
		}
		if(! is_array($return_json)) {
			$return_json = json_decode($return_json, 1);
		}
		if(empty($return_json)) {
			echo "返回格式解析结果为空" . LF;
			return;
		}
		// var_dump($return_json);
		check_ecursive($return_json, $ret);
		// array_walk_recursive($return_json,"check");
		/*
		 * foreach ($return_json as $k => $v){ if(empty($ret[$k])){ $errors[$k] = "is null"; } }
		 */
	} else {
		echo "返回格式不能为空 <hr />" . LF;
		return;
	}
	echo "测试完毕 <hr /><br /><br />";
	return $ret;
}
function socket_file($filename){
	$host = "api.daoxila.com";
	$ip = "";
	$port = 80;
	$timeout = 30;
	$block = 0;
	$limit = 0;
	
	$path = "/var/www/daoxila/socket/app/$filename.txt";
	if(! file_exists($path)) {
		exit('文件不存在');
	}
	$request = file_get_contents($path);
	$request = str_replace("https://$host", "", $request);
	$arr = explode("\r\n\r\n", $request);
	if(! empty($arr[1])) {
		$fh = fopen($path, "rb");
		$first_line = fgets($fh);
		$first_line = explode(' ', $first_line);
		$method = $first_line[0];
		$url = $first_line[1];
		
		echo <<<HTML
		<form id="form1" name="form1" method="$method" action="$url" target="_blank">
				<table>
HTML;
		$data = explode('&', $arr[1]);
		foreach($data as $k => $v) {
			$a = explode('=', $v);
			$name = $a[0];
			$value = $a[1];
			echo <<<HTML
<tr><td>$name : </td><td><input type='text' name = '$name' value='$value'></td> </tr>
HTML;
		}
		
		echo <<<HTML
		</table>
		<input type="submit" name="button"   value="提交" id="send" /><br /></form>
HTML;
		
		// var_dump($data);exit;
	}
	$fpflag = 0;
	if(! $fp = fsocketopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout)) {
		$context = array(
				'http' => array(
						'method' => $post ? 'POST' : 'GET',
						'header' => $header,
						'content' => $post,
						'timeout' => $timeout 
				) 
		);
		$context = stream_context_create($context);
		$fp = @fopen($scheme . '://' . ($ip ? $ip : $host) . ':' . $port . $path, 'b', false, $context);
		$fpflag = 1;
	}
	
	if(! $fp) {
		return '';
	}
	stream_set_blocking($fp, 0);
	// stream_set_timeout($fp, $timeout);
	@fwrite($fp, $request);
	$st = gettimeofday(1);
	while(true) {
		
		$line = fgets($fp);
		// echo $line;
		if($line == "\r\n") {
			break;
		}
	}
	
	// echo gettimeofday(1) - $st;
	
	// $status = stream_get_meta_data($fp);
	// if(!$status['timed_out']) {
	/*
	 * while(! feof($fp)) { $header = @fgets($fp); $headers .= $header; if($header && ($header == "\r\n" || $header == "\n")) { break; } }
	 */
	// var_dump($header);
	// $limit = 2;
	if($limit) {
		$return = stream_get_contents($fp, $limit);
	} else {
		$return = stream_get_contents($fp);
	}
	// }
	@fclose($fp);
	// var_dump($return);exit;
	// $return = unchunk($return);
	return $return;
}

/**
 * 去除chunk
 * 
 * @param unknown $result        	
 * @return mixed
 */
function unchunk($result){
	return preg_replace_callback('/(?:(?:\r\n|\n)|^)([0-9A-F]+)(?:\r\n|\n){1,2}(.*?)' . '((?:\r\n|\n)(?:[0-9A-F]+(?:\r\n|\n))|$)/si', create_function('$matches', 'return hexdec($matches[1]) == strlen($matches[2]) ? $matches[2] : $matches[0];'), $result);
}

/**
 * 去除chunk
 * 
 * @param string $str        	
 * @return boolean string
 */
function unchunkHttpResponse($str = null){
	if(! is_string($str) or strlen($str) < 1) {
		return false;
	}
	$eol = "\r\n";
	$add = strlen($eol);
	$tmp = $str;
	$str = '';
	do {
		$tmp = ltrim($tmp);
		$pos = strpos($tmp, $eol);
		if($pos === false) {
			return false;
		}
		$len = hexdec(substr($tmp, 0, $pos));
		if(! is_numeric($len) or $len < 0) {
			return false;
		}
		$str .= substr($tmp, ($pos + $add), $len);
		$tmp = substr($tmp, ($len + $pos + $add));
		$check = trim($tmp);
	} while(! empty($check));
	unset($tmp);
	return $str;
}


function getCombinationToString($arr, $m){
	$result = array();
	if($m == 1) {
		return $arr;
	}

	if($m == count($arr)) {
		$result[] = implode(',', $arr);
		return $result;
	}

	$temp_firstelement = $arr[0];
	unset($arr[0]);
	$arr = array_values($arr);
	$temp_list1 = getCombinationToString($arr, ($m - 1));

	foreach($temp_list1 as $s) {
		$s = $temp_firstelement . ',' . $s;
		$result[] = $s;
	}
	unset($temp_list1);

	$temp_list2 = getCombinationToString($arr, $m);
	foreach($temp_list2 as $s) {
		$result[] = $s;
	}
	unset($temp_list2);

	return $result;
}




//php命令行下加颜色
function echo_color($text, $color="NORMAL",$is_backgroud = 0, $back=0){
	$_colors = array(
			'LIGHT_RED'      => "[1;31m",
			'LIGHT_GREEN'     => "[1;32m",
			'YELLOW'     => "[1;33m",
			'LIGHT_BLUE'     => "[1;34m",
			'MAGENTA'     => "[1;35m",
			'LIGHT_CYAN'     => "[1;36m",
			'WHITE'     => "[1;37m",
			'NORMAL'     => "[0m",
			'BLACK'     => "[0;30m",
			'RED'         => "[0;31m",
			'GREEN'     => "[0;32m",
			'BROWN'     => "[0;33m",
			'BLUE'         => "[0;34m",
			'CYAN'         => "[0;36m",
			'BOLD'         => "[1m",
			'UNDERSCORE'     => "[4m",
			'REVERSE'     => "[7m",
	
	);
	
	if($is_backgroud){
		$_colors = array(
				"SUCCESS" => "[42m", //Green background
				"FAILURE" => "[41m", //Red background
				"WARNING" => "[43m", //Yellow background
				"NOTE"    => "[44m", //Blue background
		);
	}
	
	
	
	//echo chr(27) . "[0;31m" . "$text" . chr(27) . "[0m";exit;
	$out = $_colors["$color"];
	if($out == ""){ $out = "[0m"; }
	if($back){
		return chr(27).$out.$text.chr(27)."[0m";
	}else{
		echo chr(27).$out.$text.chr(27)."[0m";
		//echo chr(27)."$out$text".chr(27).chr(27)."[0m".chr(27); 
	}//fi
}


//echo colorize("Your command was successfully executed...", "SUCCESS");
function colorize($text, $status) {
	$out = "";
	switch($status) {
		case "SUCCESS":
			$out = "[42m"; //Green background
			break;
		case "FAILURE":
			$out = "[41m"; //Red background
			break;
		case "WARNING":
			$out = "[43m"; //Yellow background
			break;
		case "NOTE":
			$out = "[44m"; //Blue background
			break;
		default:
			throw new Exception("Invalid status: " . $status);
	}
	return chr(27) . "$out" . "$text" . chr(27) . "[0m";
}






if (!function_exists('getallheaders')){
    function getallheaders($raw=false) {
        $headers = '';
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        if($raw){
            $str = "";
            foreach ($headers as $k => $v){
                $str .= "$k: $v\r\n";
            }
            return $str;
        }
        return $headers;
    }
}

 