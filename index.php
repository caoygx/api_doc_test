<?php
define('LF',"\n");
if(isset($_SERVER['HTTP_HOST'])){
    $http_host =$_SERVER['HTTP_HOST'];
        if(filter_var($http_host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false){
            $domain = $http_host;
        }else{
            $arr = explode('.',$http_host);
            $c = count($arr);
            $domain = $arr[$c-2].'.'.$arr[$c-1];
        }
}else{
    $domain = '51cihai.com';
}

define('DOMAIN',$domain);

//ob_start();
define('ENV',"dev"); //上线开关
if(ENV == "dev")	  define('SUFFIX',"_dev");
elseif(ENV == "test") define('SUFFIX',"_test");
else                  define('SUFFIX',"");

define('ROOT',__DIR__);
define('APP_DEBUG',true);
define('APP_NAME', 'Home');
define('APP_PATH',ROOT.'/Application/');
define('RUNTIME_PATH',ROOT.'/Runtime/'); //runtime目录
require '../ThinkPHP/ThinkPHP.php';
