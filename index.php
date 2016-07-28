<?php
//ob_start();
define('ENV',"dev"); //上线开关
if(ENV == "dev")	  define('SUFFIX',"_dev");
elseif(ENV == "test") define('SUFFIX',"_test");
else                  define('SUFFIX',"");


define('ROOT',__DIR__);
define('PROTECTED_ROOT',__DIR__."/protected");
define('APP_DEBUG',true);
define('APP_NAME', 'Home');
define('APP_PATH',ROOT.'/Application/');
define('RUNTIME_PATH',PROTECTED_ROOT.'/Runtime/'); //runtime目录
require PROTECTED_ROOT.'/ThinkPHP/ThinkPHP.php';
