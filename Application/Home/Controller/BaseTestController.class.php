<?php
namespace Home\Controller;
use Common\CommonController;
class BaseTestController extends CommonController{
	public function __construct(){
		parent::__construct();
		$this->version = I('server.HTTP_VERSION');

	}
	
	function _initialize() {
		parent::_initialize();

        //禁用访问黑名单中的controller
        $bl_control = C('blacklist.controller');
        if(in_array(strtolower(CONTROLLER_NAME),$bl_control)){
            $this->error('control 非法访问');
        }

        //禁用访问黑名单中的action
        $bl_action = C('blacklist.action');
        if(in_array(strtolower(ACTION_NAME),$bl_action)){
            $this->error('action 非法访问');
        }

        $bl_url = C('blacklist.url');
        $current_url = CONTROLLER_NAME.'/'.ACTION_NAME;
        if(in_array(strtolower($current_url),$bl_url)) {
            $this->error('url 非法访问');
        }


    }



	/**
	 * 简版验证，上面的验证想的太复杂了
	 * @param array $param 数据源
	 * @param array $field 验证的字段
	 */
	function validate($param,$field){
	    foreach ($field as $v){
	        empty($param[$v]) && $this->error($v."不能为空");
	    }
	}

}
