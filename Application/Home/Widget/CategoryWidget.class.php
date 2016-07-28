<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Widget;
use Think\Controller;

/**
 * 分类widget
 * 用于动态调用分类信息
 */

class CategoryWidget extends Controller{
	
	/* 显示指定分类的同级分类或子分类列表 */
	public function menu(){
		$configs = C();
		$types = $configs['service_type'];
		$this->assign('menus', $types);
		$this->display('Widget::menu');
	}
	
	public function hosts(){
		$ip = gethostbyname('api.rrbrr.com');
		echo $ip;
	}
	
	public function types(){
		$configs = C();
		$types = $configs['service_type'];
		$this->assign('types', $types);
		$this->display('Widget::types');
	}
	
}
