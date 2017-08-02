<?php
namespace Home\Controller;
use Think\Controller;
use GuzzleHttp\Client;

class IndexController extends Controller {
    function index(){
		$this->display();
	}

	function num(){
       $this->display();
    }

    function calculate(){
        /*$expectCostPrice = 6.6;
        $originalCostPrice = 10;
        $originalPurchaseQuantity = 300;
        $nowPrice = 6;*/
        $request = I('');
        extract($request);
        $needPurchaseQuantity = ($originalCostPrice*$originalPurchaseQuantity-$expectCostPrice*$originalPurchaseQuantity)/($expectCostPrice-$nowPrice);
        echo $needPurchaseQuantity;
    }



    function assertJson($result,$expect){
        if($result == $expect){
            echo '成功';
        }else{
            $this->getTrace();
            echo '错误';
        }
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
            echo $k." ".$v['file']." ({$v['line']}) ".$v['function'].LF;
        }
    }


    public function getTrue(){
        return true;
    }

    public function getFalse(){
        return false;
    }

    public function getEmptyArray(){
        return array();
    }

    public function getUnEmptyArray(){
        return array(1,2);
    }

    function test(){
        $client = new Client();
        $res = $client->request('GET', 'http://www.cnblogs.com/xp796/p/6444106.html');
        echo $res->getStatusCode(); // "200"
        echo $res->getHeader('content-type'); // 'application/json; charset=utf8'
        echo $res->getBody(); // {"type":"User"...' // 发送一个异步请求 $request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org'); $promise = $client->sendAsync($request)->then(function ($response) { echo 'I completed! ' . $response->getBody(); }); $promise->wait();


    }

	

}