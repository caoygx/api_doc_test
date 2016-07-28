<?php
$parentDir = dirname(ROOT);
require($parentDir.'/config/db_master_config.inc.php');
$pubConfig = array(
	'DB_TYPE' => 'mysql',
	'DB_PREFIX' => 'ex_',
	'REDIS_HOST' => "redis-server",
	'DATA_CRYPT_TYPE' => 'DxlMcrypt',
	
	'REDIS_KEY' => array(
		'pack_android' => 'redis_album_android_list',
    	'pack_iphone' => 'redis_album_iphone_list',
	)
);

$dev = array(
	'SHOW_PAGE_TRACE'=>true,
    'DB_HOST' => '192.168.22.6',
    'DB_NAME' => 'dxl_event',
    'DB_USER' => 'dxl_dev',
    'DB_PWD' => 'Ze2bUs5zMob1',
	
	"main" =>  array(
		
		'DB_HOST' => DB_HOST,
		'DB_USER' => DB_USER,
		'DB_PWD'  => DB_PWD,
		'DB_NAME' => 'dxl_exodus_dev',
		'DB_TYPE' => "mysql",
	),
	
	"api" =>  array(
		'DB_HOST' => DB_HOST,
		'DB_USER' => DB_USER,
		'DB_PWD'  => DB_PWD,
		'DB_NAME' => 'dxl_api',
		'DB_TYPE' => "mysql",
	),
		
);

$test = array(
	'SHOW_PAGE_TRACE'=>true,

    'DB_HOST' => '192.168.22.6',
    'DB_NAME' => 'dxl_event_test',
    'DB_USER' => 'dxl_test',
    'DB_PWD' => '8V8S5evXY8l5',
);

$online = array(
    'DB_HOST' => DB_HOST,
    'DB_USER' => DB_USER,
    'DB_PWD'  => DB_PWD,
	'DB_NAME' => 'dxl_event',
	
	"main" =>  array(
		'DB_HOST' => DB_HOST,
		'DB_USER' => DB_USER,
		'DB_PWD'  => DB_PWD,
		'DB_NAME' => 'dxl_exodus_dev',
	),
	
	"api" =>  array(
		'DB_HOST' => DB_HOST,
		'DB_USER' => DB_USER,
		'DB_PWD'  => DB_PWD,
		'DB_NAME' => 'dxl_api',
	),
		
);






if(ENV == "dev"){
	return array_merge($pubConfig,$dev);
}elseif(ENV == "test"){
	return array_merge($pubConfig,$test);
}elseif(ENV == "online"){
	return array_merge($pubConfig,$online);
}



