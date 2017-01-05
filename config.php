<?php
if(IS_CLI){
	define('LF',"\n");
}else{
	define('LF',"<br />");
}
$parentDir = dirname(ROOT);
//var_dump( getenv('ENV_PATH'));exit('x');
define('URL_API',"http://vpn.lezreader.com");
//define('URL_API',"http://api.rrbrr.com");
$pubConfig = array(
	'TAGLIB_PRE_LOAD'    =>    'html',
    'URL_MODEL'=>3, //默认1;URL模式：0 普通模式 1 PATHINFO 2 REWRITE 3 兼容模式
	//'LOG_RECORD'=>true, 
	'DEFAULT_LANG' => 'en-us',
	'DB_HOST' => "127.0.0.1",
    'DB_NAME' => 'api',
    'DB_USER' => "root",
    'DB_PWD'  => "",
    'DB_TYPE' => 'mysql',
    'DB_PREFIX' => 'vpn_',
    'DB_CHARSET'=>'utf8',
		
	'service_type' =>  array( '全局' , '通用' , '助手' ),
		
		
		
	"api" =>  array(
		'DB_HOST' => "127.0.0.1",
		'DB_USER' => "root",
		'DB_PWD'  => "",
		'DB_NAME' => 'api',
		'DB_TYPE' => "mysql",
		'DB_CHARSET'=>'utf8',
	    'DB_PREFIX' => 'vpn_',
	),
	'options' => array(
		"bug_status"=>array ( 1 => '已收单', 2 => '已分级', 3 => '已分配', 4 => '已定位', 5 => '解决中', 6 => '已解决', 7 => '已上线', 8 => '已完结', 9 => '不是bug', )
	),
	
 
	
);

$dev = array(
	//'SHOW_PAGE_TRACE'=>true,
	'FIRE_SHOW_PAGE_TRACE' => true,
/*    'DB_HOST' => '192.168.22.6',
    'DB_NAME' => 'dxl_event',
    'DB_USER' => 'dxl_dev',
    'DB_PWD' => 'Ze2bUs5zMob1',
*/	

	
);



$online = array(
	
		
);





$test = array(
//	'SHOW_PAGE_TRACE'=>true,
    'DB_HOST' => '192.168.22.6',
    'DB_NAME' => 'dxl_event_test',
    'DB_USER' => 'dxl_test',
    'DB_PWD' => '8V8S5evXY8l5',
);
if(ENV == "dev"){
	return array_merge($pubConfig,$dev);
}elseif(ENV == "test"){
	return array_merge($pubConfig,$test);
}elseif(ENV == "online"){
	 
	return array_merge($pubConfig,$online);
}



