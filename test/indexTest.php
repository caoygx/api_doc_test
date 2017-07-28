<?php
class IndexTest extends PHPUnit_Framework_TestCase{
    //构造函数
    function __construct(){
        define('TP_BASEPATH', '/www/web');
        include_once __DIR__.'/base.php';
        Vendor('Guzzle.autoload');
        //导入要测试的控制器
        //include_once '/www/web/TPUNIT/Application/Home/Controller/IndexController.class.php';
    }

    function test(){

        //require '/www/web/video/ThinkPHP/Library/Vendor/Guzzle/autoload.php';
        $client = new \GuzzleHttp\Client();
        //$res = $client->request('GET', 'https://api.github.com/repos/guzzle/guzzle');
        $url = 'http://121.40.231.207:8081/transfer/queryOrder?order_id=123&sign=111';
        $res = $client->request('GET', $url);
        $result =  $res->getStatusCode();
        $this->assertStatus($result,'300');
// 200
        //echo $res->getHeaderLine('content-type');
// 'application/json; charset=utf8'
        $json = '{"code":200,"data":{"status":"1","real_price":"17.68"},"sign":"9f4a8cdff9222d3325c03afada72967b"}';
        $body =  $res->getBody($result);
        $this->assertTrue($body,$json);

        exit;
// '{"id": 1420053, "name": "guzzle", ...}'

// Send an asynchronous request.
        $request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org');
        $promise = $client->sendAsync($request)->then(function ($response) {
            echo 'I completed! ' . $response->getBody();
        });
        $promise->wait();

    }

    function testLogin(){

    }



        function assertStatus($result,$expect){


            /*if($back)
                return $msg;
            else
                echo $msg."\n";

            return $return;*/

            if($result == $expect){
                echo '成功';
            }else{
                $this->getTrace();
                echo '错误';
            }
        }

        function getTrace(){
            $time = time();
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            foreach ($trace as $k => $v){
                echo $k." ".$v['file']." ({$v['line']}) ".$v['function']."\n";
            }
        }
}

$a = new IndexTest();
$a->test();