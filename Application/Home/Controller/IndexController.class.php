<?php
namespace Home\Controller;
use Think\Controller;

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

	

}