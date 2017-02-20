<?php
// +----------------------------------------------------------------------
// | Driving school management system [ DSMS ]
// +----------------------------------------------------------------------
// | Copyright (c) 2012 MarkDream.com All rights reserved.
// +----------------------------------------------------------------------
// | Link ( http://www.markdream.com )
// +----------------------------------------------------------------------
// | Author: Jxcent <jxcent@gmail.com>
// +----------------------------------------------------------------------
// $Id: QQHelper.class.php	 2012-9-2 下午04:43:22Z	Jxcent $
namespace Org\Util;
defined('THINK_PATH') or exit();

/**
 * 此类QQ互联类，负责获取用户的openid 和 访问令牌
 * openid 相当于QQ号  一个QQ号对应唯一一个openid
 * 访问令牌 +获取当前应用+openid来实现某种操作 例如分享、发说说、传图……具体可以参考
 * http://wiki.opensns.qq.com/wiki/%E3%80%90QQ%E7%99%BB%E5%BD%95%E3%80%91%E6%96%87%E6%A1%A3%E8%B5%84%E6%BA%90
 * @author Jxcent
 *
 */
class QQHelper {
	//QQ登录
	function login($appid, $scope, $callback) {
		$_SESSION [SES_STATE_NAME] = md5 ( uniqid ( rand (), true ) ); //CSRF protection
		$login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" . $appid . "&redirect_uri=" . urlencode ( $callback ) . "&state=" . $_SESSION [SES_STATE_NAME] . "&scope=" . $scope;
		header ( "Location:$login_url" );
	}
	//登录成功回调函数 目的就是获取访问令牌
	function callback($path) {
		//var_dump($_SESSION);
		if ($_REQUEST ['state'] == $_SESSION [SES_STATE_NAME]) {
			$token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&" . "client_id=" . APP_ID . "&redirect_uri=" . urlencode ( $path ) . "&client_secret=" . APP_KEY . "&code=" . $_REQUEST ["code"];
			
			$response = get_url_contents ( $token_url );
			if (strpos ( $response, "callback" ) !== false) {
				$lpos = strpos ( $response, "(" );
				$rpos = strrpos ( $response, ")" );
				$response = substr ( $response, $lpos + 1, $rpos - $lpos - 1 );
				$msg = json_decode ( $response );
				if (isset ( $msg->error )) {
					echo "<h3>错误代码:</h3>" . $msg->error;
					echo "<h3>信息  :</h3>" . $msg->error_description;
					exit ();
				}
			}
			
			$params = array ();
			parse_str ( $response, $params );
			$_SESSION [SES_TOKEN_NAME] = $params ["access_token"];
		
		} else {
			echo ("The state does not match. You may be a victim of CSRF.");
		}
	}
	//获取该QQ用户的openid
	function get_openid() {
		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $_SESSION [SES_TOKEN_NAME];
		
		$str = get_url_contents ( $graph_url );
		if (strpos ( $str, "callback" ) !== false) {
			$lpos = strpos ( $str, "(" );
			$rpos = strrpos ( $str, ")" );
			$str = substr ( $str, $lpos + 1, $rpos - $lpos - 1 );
		}
		
		$user = json_decode ( $str );
		if (isset ( $user->error )) {
			echo "<h3>错误代码:</h3>" . $user->error;
			echo "<h3>信息  :</h3>" . $user->error_description;
			exit ();
		}
		$_SESSION [SES_OPENID_NAME] = $user->openid;
	}
	
	//获取用户信息
	function get_user_info() {
		$get_user_info = "https://graph.qq.com/user/get_user_info?" . "access_token=" . $_SESSION [SES_TOKEN_NAME] . "&oauth_consumer_key=" . APP_ID . "&openid=" . $_SESSION [SES_OPENID_NAME] . "&format=json";
		
		return get_url_contents ( $get_user_info );
	}
	
	//添加微博,必须在u.rrbrr.com正面调用，否则无法获取到SES_OPENID_NAME值，或把SES_OPENID_NAME定义放到公共config.php里
	//发微博
	function add_weibo(){
		Vendor('qqConnect\qqConnectAPI');
		$qc = new QC($_SESSION [SES_TOKEN_NAME],$_SESSION [SES_OPENID_NAME]);
 		//$qc->get_openid();
		$_POST['img'] = urlencode($_POST['img']);
		$ret = $qc->add_t($_POST);
		
		return $ret;
	}
	
	//发qq空间
	function add_topic(){
		Vendor('qqConnect\qqConnectAPI');
		
		$_POST['richtype'] = 3;
		$_POST['richval'] = "http://v.youku.com/v_playlist/f16588713o1p0.html";
		//$_POST['con'] = "真不错";
		$_POST['lbs_nm'] = "腾讯大厦";
		$_POST['lbs_x'] = "39.909407";
		$_POST['lbs_y'] = "116.397521";
		$_POST['third_source'] = "1";
		$_POST['format'] = "json";

		
		$qc = new QC($_SESSION [SES_TOKEN_NAME],$_SESSION [SES_OPENID_NAME]);
 		//$_POST['img'] = urlencode($_POST['img']);
		$ret = $qc->add_topic($_POST);
		return $ret;
	}
	
}

