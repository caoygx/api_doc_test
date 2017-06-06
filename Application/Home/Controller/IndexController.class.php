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


	

}