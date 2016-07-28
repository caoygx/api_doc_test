<?php
function ri($str){
	exit(var_dump($str));
}
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
	if(! isset($api['get'])) {
		$api['get'] = 1;
	}
	if(!is_array($api['data'])){
		$api['data'] = json_decode($api['data'],true);
	}
	$method = $api['get'] ? "GET" : "POST";
	$parms = $api['data'];
	$id = $api['id'];
	$ret = curl_get_content($url, $parms, $api['get']);
	$ret = json_decode($ret,true);
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
		$text = $api['title'].":".$api['url']; //$api['title'].":".
		if(!empty($ret['http_code'])){
			echo_color("[http_status:{$ret['http_code']} $method] ".$text."    ".$ret['data'], "RED");
		}else{
			if($ret['code'] != 1){}
			echo_color("[http_status:200 $method  id:$id] ".$text, "GREEN");
		}
		echo LF;
	}
	
	$return_json = $api['return_json'];
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
	
	return $ret;
}

function analysis_result($ret){
	$ret = '{"count":861,"items":[{"hotel_id":5346,"id":5346,"name":"\u80e5\u5029\u6797\u9152\u5e97","url":"xuqianlinjiudian","city_id":7,"region":"\u6768\u6d66\u533a","city_name":"\u4e0a\u6d77","district_name":"\u6768\u6d66\u533a","address":"\u7684\u662f\u4f10\u56de\u590d\u4e0a","status":2,"class_name":"\u661f\u7ea7\u9152\u5e97","latitude":"0","longitude":"0","menu_price_min":20,"menu_price_max":20,"desk_recommend_min":"0","desk_recommend_max":77,"total_rate":"","images_num":0,"comment":"comment","entityType":"hotelbiz","cover_image":"2015-09\/20150914144282076.jpg","fanXian":0,"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":2,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":1,"card_text":"\u901a\u8fc7\u5230\u559c\u5566\u9884\u5b9a\uff0c\u7acb\u4eab10%\u8fd4\u5229\uff01","fu_flag":"1","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":3}],"pay":{"icon":0,"text":"\u5728\u7ebf\u652f\u4ed8\uff0c\u7acb\u4eab9\u6298"}},{"hotel_id":4618,"id":4618,"name":"testyt","url":"testyt","city_id":7,"region":"\u9ec4\u6d66\u533a","city_name":"\u4e0a\u6d77","district_name":"\u9ec4\u6d66\u533a","address":"\u66f9\u9633\u8def335\u53f7","status":2,"class_name":"\u7279\u8272\u9910\u5385","latitude":"0","longitude":"0","menu_price_min":2333,"menu_price_max":2333,"desk_recommend_min":"0","desk_recommend_max":11,"total_rate":"","images_num":8,"comment":"comment","entityType":"hotelbiz","cover_image":"2014-10\/20141024153532840.jpg","fanXian":0,"payConfig":{"royalty_rule":"1^7","cashback_rule":"1^10","discount_rule":"","royalty_rule_parsed":"7","cashback_rule_parsed":"10"},"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":0,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":0,"card_text":"","fu_flag":"0","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":2}],"pay":{}},{"hotel_id":5027,"id":5027,"name":"\u597d\u65e5\u5b50","url":"www.baidu.com333","city_id":7,"region":"\u8679\u53e3\u533a","city_name":"\u4e0a\u6d77","district_name":"\u8679\u53e3\u533a","address":"\u6768\u6d66\u533a\u653f\u7acb\u8def","status":2,"class_name":"\u661f\u7ea7\u9152\u5e97","latitude":"0","longitude":"0","menu_price_min":0,"menu_price_max":0,"desk_recommend_min":"0","desk_recommend_max":23,"total_rate":"","images_num":1,"comment":"comment","entityType":"hotelbiz","cover_image":"","fanXian":0,"payConfig":[],"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":0,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":0,"card_text":"","fu_flag":"0","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":2}],"pay":{}},{"hotel_id":5343,"id":5343,"name":"\u5f20\u6653\u96e8\u5ea6\u5047\u9152\u5e97","url":"zxy1991","city_id":7,"region":"\u6768\u6d66\u533a","city_name":"\u4e0a\u6d77","district_name":"\u6768\u6d66\u533a","address":"\u56fd\u5b9a\u4e1c\u8def","status":2,"class_name":"\u661f\u7ea7\u9152\u5e97","latitude":"0","longitude":"0","menu_price_min":5000,"menu_price_max":5000,"desk_recommend_min":"0","desk_recommend_max":55,"total_rate":"","images_num":0,"comment":"comment","entityType":"hotelbiz","cover_image":"","fanXian":0,"payConfig":{"royalty_rule":"1^7","cashback_rule":"1^10","discount_rule":"","royalty_rule_parsed":"7","cashback_rule_parsed":"10"},"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":0,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":0,"card_text":"","fu_flag":"0","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":2}],"pay":{}},{"hotel_id":14,"id":14,"name":"A\u5409\u745e\u9152\u5e97","url":"jiruijiudian","city_id":7,"region":"\u6d66\u4e1c\u65b0\u533a","city_name":"\u4e0a\u6d77","district_name":"\u6d66\u4e1c\u65b0\u533a","address":"\u6d66\u7535\u8def110\u53f7(\u8fd1\u6d66\u4e1c\u5357\u8def)","status":2,"class_name":"\u661f\u7ea7\u9152\u5e97","latitude":"0","longitude":"0","menu_price_min":1888,"menu_price_max":4888,"desk_recommend_min":"0","desk_recommend_max":15,"total_rate":"","images_num":7,"comment":"comment","entityType":"hotelbiz","cover_image":"2014-09\/20140917094510.jpg","fanXian":0,"gift_flag":1,"is_gift":1,"coupon_flag":1,"tollMode":2,"sign_flag":1,"is_event":1,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"1447258","card_flag":1,"card_text":"\u901a\u8fc7\u5230\u559c\u5566\u9884\u5b9a\uff0c\u7acb\u4eab10%\u8fd4\u5229\uff01","fu_flag":"1","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":4}],"pay":{"icon":0,"text":"\u5728\u7ebf\u652f\u4ed8\uff0c\u7acb\u4eab9\u6298"}},{"hotel_id":84,"id":84,"name":"\u84dd\u8446\u5fb7\u00b7\u8a89\u8bfa\u79c1\u4eba\u5a5a\u793c\u4f1a\u99861122","url":"lanbaodeyunuo","city_id":7,"region":"\u9759\u5b89\u533a","city_name":"\u4e0a\u6d77","district_name":"\u9759\u5b89\u533a","address":"\u5f90\u5bb6\u6c47\u8def618\u53f7(\u65e5\u6708\u5149\u4e2d\u5fc3\u5e7f\u573a5-6\u697c)1","status":2,"class_name":"\u5a5a\u793c\u4f1a\u6240","latitude":"0","longitude":"0","menu_price_min":4385,"menu_price_max":6981,"desk_recommend_min":"0","desk_recommend_max":40,"total_rate":"","images_num":8,"comment":"comment","entityType":"hotelbiz","cover_image":"2014-12\/20141216165746187.jpg","fanXian":0,"payConfig":{"royalty_rule":"1^7","cashback_rule":"1^0","discount_rule":"","royalty_rule_parsed":"7","cashback_rule_parsed":"0"},"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":1,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":0,"card_text":"","fu_flag":"1","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":2}],"pay":{"icon":0,"text":"\u5728\u7ebf\u652f\u4ed8\uff0c\u7acb\u4eab9\u6298"}},{"hotel_id":4727,"id":4727,"name":"strdyteswte","url":"wtwttet","city_id":7,"region":"\u6768\u6d66\u533a","city_name":"\u4e0a\u6d77","district_name":"\u6768\u6d66\u533a","address":"wtrwrwsdrwerrw","status":2,"class_name":"\u7279\u8272\u9910\u5385","latitude":"0","longitude":"0","menu_price_min":0,"menu_price_max":0,"desk_recommend_min":"0","desk_recommend_max":0,"total_rate":"","images_num":0,"comment":"comment","entityType":"hotelbiz","cover_image":"","fanXian":0,"payConfig":[],"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":0,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":0,"card_text":"","fu_flag":"0","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":2}],"pay":{}},{"hotel_id":22,"id":22,"name":"\u8001\u6d0b\u623f","url":"LaoYangFang","city_id":7,"region":"\u5362\u6e7e\u533a","city_name":"\u4e0a\u6d77","district_name":"\u5362\u6e7e\u533a","address":"\u7ecd\u5174\u8def27\u53f7","status":2,"class_name":"\u7279\u8272\u9910\u5385","latitude":"0","longitude":"0","menu_price_min":3880,"menu_price_max":5580,"desk_recommend_min":"0","desk_recommend_max":23,"total_rate":"","images_num":5,"comment":"comment","entityType":"hotelbiz","cover_image":"2015-07\/20150710110116825.jpg","fanXian":0,"gift_flag":1,"is_gift":1,"coupon_flag":1,"tollMode":2,"sign_flag":1,"is_event":1,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"\u5230\u559c\u5566\u4f1a\u5458\u670d\u52a1\u8d39\u7279\u60e0","card_flag":1,"card_text":"\u901a\u8fc7\u5230\u559c\u5566\u9884\u5b9a\uff0c\u7acb\u4eab6.8%\u8fd4\u5229\uff01","fu_flag":"1","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":4}],"pay":{"icon":0,"text":"\u5728\u7ebf\u652f\u4ed8\uff0c\u7acb\u4eab9\u6298"}},{"hotel_id":3292,"id":3292,"name":"\u82b1\u5ac1\u4e3d\u820d\u79c1\u4eba\u5a5a\u793c\u4f1a\u6240\uff08\u4e94\u89d2\u573a\u5e97\uff09","url":"HuaJiaLiShe-WuJiaoChang","city_id":7,"region":"\u6768\u6d66\u533a","city_name":"\u4e0a\u6d77","district_name":"\u6768\u6d66\u533a","address":"\u56fd\u6743\u8def49\u53f7","status":2,"class_name":"\u5a5a\u793c\u4f1a\u6240","latitude":"0","longitude":"0","menu_price_min":6777,"menu_price_max":6777,"desk_recommend_min":"0","desk_recommend_max":30,"total_rate":"","images_num":11,"comment":"comment","entityType":"hotelbiz","cover_image":"2015-02\/20150212150356764.jpg","fanXian":0,"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":2,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":1,"card_text":"\u901a\u8fc7\u5230\u559c\u5566\u9884\u5b9a\uff0c\u7acb\u4eab17%\u8fd4\u5229\uff01","fu_flag":"1","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":3}],"pay":{"icon":0,"text":"\u5728\u7ebf\u652f\u4ed8\uff0c\u7acb\u4eab9\u6298"}},{"hotel_id":49,"id":49,"name":"\u827e\u798f\u6566\u9152\u5e97","url":"aifudun","city_id":7,"region":"\u6d66\u4e1c\u65b0\u533a","city_name":"\u4e0a\u6d77","district_name":"\u6d66\u4e1c\u65b0\u533a","address":"\u6d66\u660e\u8def1888\u53f7","status":2,"class_name":"\u661f\u7ea7\u9152\u5e97","latitude":"0","longitude":"0","menu_price_min":4588,"menu_price_max":4588,"desk_recommend_min":"0","desk_recommend_max":234,"total_rate":"","images_num":12,"comment":"comment","entityType":"hotelbiz","cover_image":"2014-09\/20140911153947.jpg","fanXian":0,"payConfig":[],"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":0,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":0,"card_text":"","fu_flag":"0","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":2}],"pay":{}},{"hotel_id":71,"id":71,"name":"\u5916\u6ee9\u8302\u60a6\u5927\u9152\u5e97","url":"waitanmaoyue","city_id":7,"region":"\u8679\u53e3\u533a","city_name":"\u4e0a\u6d77","district_name":"\u8679\u53e3\u533a","address":"\u9ec4\u6d66\u8def199\u53f7","status":2,"class_name":"\u661f\u7ea7\u9152\u5e97","latitude":"0","longitude":"0","menu_price_min":5000,"menu_price_max":9888,"desk_recommend_min":"0","desk_recommend_max":45,"total_rate":"","images_num":12,"comment":"comment","entityType":"hotelbiz","cover_image":"2015-06\/20150616133945136.jpg","fanXian":0,"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":2,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":1,"card_text":"\u901a\u8fc7\u5230\u559c\u5566\u9884\u5b9a\uff0c\u7acb\u4eab10%\u8fd4\u5229\uff01","fu_flag":"1","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":3}],"pay":{"icon":0,"text":"\u5728\u7ebf\u652f\u4ed8\uff0c\u7acb\u4eab9\u6298"}},{"hotel_id":87,"id":87,"name":"\u4e16\u7eaa\u7d2b\u6f9c\u4f1a\u9986","url":"shijizilan","city_id":7,"region":"\u6d66\u4e1c\u65b0\u533a","city_name":"\u4e0a\u6d77","district_name":"\u6d66\u4e1c\u65b0\u533a","address":"\u9526\u7ee3\u8def1001\u53f7(\u4e16\u7eaa\u516c\u56ed2\u53f7\u95e8\u5185\u8fd1\u4e16\u7eaa\u5927\u9053)","status":2,"class_name":"\u7279\u8272\u9910\u5385","latitude":"0","longitude":"0","menu_price_min":4388,"menu_price_max":6888,"desk_recommend_min":"0","desk_recommend_max":50,"total_rate":"","images_num":29,"comment":"comment","entityType":"hotelbiz","cover_image":"2015-06\/20150616133936183.jpg","fanXian":0,"payConfig":[],"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":0,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":0,"card_text":"","fu_flag":"0","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":2}],"pay":{}},{"hotel_id":95,"id":95,"name":"\u5f90\u6c47\u745e\u5cf0\u9152\u5e97","url":"xuhuiruifeng","city_id":7,"region":"\u5f90\u6c47\u533a","city_name":"\u4e0a\u6d77","district_name":"\u5f90\u6c47\u533a","address":"\u8087\u5bb6\u6d5c\u8def7\u53f7","status":2,"class_name":"\u661f\u7ea7\u9152\u5e97","latitude":"0","longitude":"0","menu_price_min":2988,"menu_price_max":4688,"desk_recommend_min":"0","desk_recommend_max":30,"total_rate":"","images_num":7,"comment":"comment","entityType":"hotelbiz","cover_image":"2012-11\/20121106175420.jpg","fanXian":0,"payConfig":[],"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":0,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":0,"card_text":"","fu_flag":"0","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":2}],"pay":{}},{"hotel_id":105,"id":105,"name":"\u5927\u534e\u9526\u7ee3\u5047\u65e5\u9152\u5e97","url":"dahuajinxiu","city_id":7,"region":"\u6d66\u4e1c\u65b0\u533a","city_name":"\u4e0a\u6d77","district_name":"\u6d66\u4e1c\u65b0\u533a","address":"\u9526\u5c0a\u8def399\u53f7","status":2,"class_name":"\u661f\u7ea7\u9152\u5e97","latitude":"0","longitude":"0","menu_price_min":2999,"menu_price_max":5888,"desk_recommend_min":"0","desk_recommend_max":32,"total_rate":"","images_num":8,"comment":"comment","entityType":"hotelbiz","cover_image":"2012-09\/20120904112638.jpg","fanXian":0,"payConfig":[],"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":0,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":0,"card_text":"","fu_flag":"0","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":2}],"pay":{}},{"hotel_id":112,"id":112,"name":"\u4e1c\u6021\u5927\u9152\u5e97","url":"dongyi","city_id":7,"region":"\u6d66\u4e1c\u65b0\u533a","city_name":"\u4e0a\u6d77","district_name":"\u6d66\u4e1c\u65b0\u533a","address":"\u4e01\u9999\u8def555\u53f7","status":2,"class_name":"\u661f\u7ea7\u9152\u5e97","latitude":"0","longitude":"0","menu_price_min":1111,"menu_price_max":5288,"desk_recommend_min":"0","desk_recommend_max":18,"total_rate":"","images_num":6,"comment":"comment","entityType":"hotelbiz","cover_image":"2015-06\/20150616133821226.jpg","fanXian":0,"gift_flag":1,"is_gift":1,"coupon_flag":0,"tollMode":2,"sign_flag":1,"is_event":0,"score":0,"gift_text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","coupon_text":"","card_flag":1,"card_text":"\u901a\u8fc7\u5230\u559c\u5566\u9884\u5b9a\uff0c\u7acb\u4eab10%\u8fd4\u5229\uff01","fu_flag":"1","is_arrival":1,"arrival_text":"\u9884\u7ea6\u5230\u5e97\u5c31\u9001\u4ef7\u503c9999\u5143\u5927\u793c\u5305","activitys":[{"icon":0,"text":"\u5230\u559c\u5566\u9884\u7ea6\u8d60\u900148882\u5143\u5927\u793c\u5305\uff01 ","num":3}],"pay":{"icon":0,"text":"\u5728\u7ebf\u652f\u4ed8\uff0c\u7acb\u4eab9\u6298"}}]}';
	
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
	global $errors;
	foreach($return_json as $k => $v) {
		if(is_array($v)) {
			check_ecursive_cli($v, $ret[$k], $prve_key . "." . $k);
		} else {
			if(empty($ret[$k]) && $ret[$k] === null) {
				echo_color("    $prve_key.{$k} : ", "BROWN");
				echo_color( var_export($ret[$k],1), "RED");
				echo LF;
				if($v === "not null") {
					echo "$k 不能为空";
				}
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
 * curl
 *
 * @param
 *        	string url
 * @param
 *        	array 数据
 * @param
 *        	int 请求超时时间
 * @param
 *        	bool HTTPS时是否进行严格认证
 * @return string
 */
function curl_get_content($url, $data = "", $method = "get", $timeout = 30, $CA = false){
	
	// $url = "http://www.baidu.com";
	$cacert = getcwd() . '/cacert.pem'; // CA根证书
	$SSL = substr($url, 0, 8) == "https://" ? true : false;
	$ch = curl_init();
	if(is_object($data)){
		$data = (array)$data;
	}

	
	$method = strtolower($method);
	if($method == 'get') {
		if(is_array($data)) {
			$data = http_build_query($data);
		}
		$url .= "?" . $data;
	} else {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //data with URLEncode
	}
	//echo $url;
	//var_dump($data);exit;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout - 2);
	if($SSL && $CA) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // 只信任CA颁布的证书
		curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布）
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名，并且是否与提供的主机名匹配
	} else if($SSL && ! $CA) {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Expect:' 
	)); // 避免data数据过长问题
	    // var_dump($data);
	
	$headerArr[] = 'PARAMS:android#1.4.2#wandoujias';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
	
	$ret = curl_exec($ch);
	if(empty($ret)) {
		var_dump(curl_error($ch)); // 查看报错信息
	}
	// var_dump($ret);
	// exit('x');
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	if($httpCode != 200) {
		$tmp = array();
		$tmp['http_code'] = $httpCode;
		$tmp['data'] = $ret;
		
		//echo "\n服务器错误：$httpCode";
		//echo $ret;
		$ret = json_encode($tmp);
	}
	curl_close($ch);
	//var_dump($ret);
	return $ret;
}

// 定义接口
/*
 * $Apis = array( // ==============================================酒店==================================================// "酒店列表(筛选，搜索，所有列表)" => array( 'url' => "/hotel/list", "get" => "1", // get请求1，post请求0 'data' => "city=$city", // 请求参数 're' => "count" //期望返回参数 )// 期望返回参数 null, "获取酒店基础信息根据id" => array( 'url' => "/hotel/detail", 'get' => '1', 'data' => "hotel_id=$hotel_id&client_id=$client_id", 're' => 'name' ), "获取酒店详细信息根据id和url" => array( 'url' => "/hotel/detail-all", 'get' => '1', 'data' => "hotel_id=$hotel_id&city=$city&clent_id=$client_id", 're' => 'name' ), "根据城市随机得到酒店" => array( 'url' => "/hotel/random", 'get' => '1', 'data' => "city=$city", 're' => "0", 'array_re' => "name" //对于只返回二维数组中第一个数组中的name )// 对于只返回二维数组中第一个数组中的name null, "获取酒店详情底板页1.3" => array( 'url' => "/hotel/detail-3", 'get' => '1', 'data' => "hotel_id=$hotel_id&client_id=$client_id", 're' => "name" ), "获取礼品列表" => array( 'url' => '/gifts', 'get' => '1', 'data' => "city=$city", 're' => '0', 'array_re' => 'name' ), "获取酒店评价" => array( 'url' => "/hotel/comments", 'get' => '1', 'data' => "hotel_id=$hotel_id", 're' => 'total_rate' //总体评分 )// 总体评分 null, '添加酒店评价' => array( 'url' => "/hotel/comments", 'get' => "0", 'data' => "hotel_id=$hotel_id&user_id=$user_id&total_rate=$total_rate&env_rate=$env_rate&menu_rate=$menu_rate&service_rate=$service_rate&comments=$comments", 're' => "code" ), '获取用户是否对酒店评价' => array( 'url' => "/hotel/comment/isComment", 'get' => '1', 'data' => "hotel_id=$hotel_id&user_id=$user_id", 're' => "msg" ), '获取酒店宴会厅列表' => array( 'url' => "/hotel/halls", 'get' => '1', 'data' => "hotel_id=$hotel_id", 're' => '0', 'array_re' => 'name' ), '获取酒店图片列表' => array( 'url' => '/hotel/image/images', 'get' => '1', 'data' => "hotel_id=$hotel_id", 're' => '0', 'array_re' => 'path' ), '获取酒店菜单列表' => array( 'url' => '/hotel/menus', 'get' => '1', 'data' => "hotel_id=$hotel_id", 're' => '0', 'array_re' => 'name' ), '获取酒店特色标签' => array( 'url' => '/hotel/features', 'get' => '1', 'data' => "hotel_id=$hotel_id", 're' => '0', 'array_re' => 'name' ), '获取酒店优惠活动列表' => array( 'url' => '/hotel/events', 'get' => '1', 'data' => "hotel_id=$hotel_id", 're' => '0', 'array_re' => 'name' ), '酒店婚宴订单下单' => array( 'url' => '/order/hotel/create', 'get' => '0', 'data' => "hotel_id=$hotel_id&mobile=$mobile&order_from=$order_from&city=$city", 're' => 'code' ), //==============================================用户==================================================// '请求发送验证码'// ==============================================用户==================================================// null => array( 'url' => '/app/request-code', 'get' => '1', 'data' => "mobile=$mobile", 're' => 'code' ), '验证验证码' => array( 'url' => '/app/verify', 'get' => '1', 'data' => "mobile=$mobile&code=$code", 're' => 'code' ), '用户登录' => array( 'url' => '/user/login', 'get' => '1', 'data' => "account=$account&password=$password", 're' => 'code' ), '短信找回密码接口' => array( 'url' => '/user/get-password', 'get' => '1', 'data' => "mobile=$mobile", 're' => 'code' ), '编辑用户信息' => array( 'url' => '/user/edit', 'get' => '0', 'data' => "user_id=$user_id&city=上海", 're' => 'code' ), '修改密码' => array( 'url' => '/user/edit-password', 'get' => '0', 'data' => "user_id=$user_id&originalpassword=$password&newpassword=$password", 're' => 'code' ), '短信注册/登录' => array( 'url' => '/user/edit', 'get' => '0', 'data' => "mobile=$mobile", 're' => 'code' ), '短信注册/登录2' => array( 'url' => '/user/edit', 'get' => '0', 'data' => "mobile=$mobile&code=$code", 're' => 'code' ), '短信验证码注册用户设置密码' => array( 'url' => '/user/set-sign-in-password', 'get' => '0', 'data' => "mobile=$mobile&password=$password&code=$code", 're' => 'code' ), '请求发送验证码(用于登录或注册)' => array( 'url' => '/user/request-code', 'get' => '0', 'data' => "mobile=$mobile", 're' => 'code' ), '通过验证码或密码登录(包含注册)' => array( 'url' => '/user/login-by-sms-or-pass', 'get' => '0', 'data' => "mobile=$mobile&code=$code", 're' => 'code' ), '新酒店列表' => array( 'url' => '/hotel/hotel/list', "get" => "1", 'data' => "city=$city", 're' => "count" ) ) ;
 */

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

