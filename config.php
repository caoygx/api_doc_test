<?php
if(IS_CLI){
	define('LF',"\n");
}else{
	define('LF',"<br />");
}
$parentDir = dirname(ROOT);
//var_dump( getenv('ENV_PATH'));exit('x');


return array(
    'SHOW_PAGE_TRACE' => true,
    'TAGLIB_PRE_LOAD' => 'htmlme', //,OT\\TagLib\\Think
    'URL_MODEL'=>2, //默认1;URL模式：0 普通模式 1 PATHINFO 2 REWRITE 3 兼容模式
	//'LOG_RECORD'=>true, 
    'DB_PREFIX' => 'vpn_',
    'DB_TYPE'    => 'sqlite',
    'DB_NAME'    => ROOT.'/doc.db',
    'DB_PREFIX' => '',
	'service_type' =>  array( '全局' , '通用' , '助手' ),

    'options' => array(
		"bug_status"=>array ( 1 => '已收单', 2 => '已分级', 3 => '已分配', 4 => '已定位', 5 => '解决中', 6 => '已解决', 7 => '已上线', 8 => '已完结', 9 => '不是bug', ),
        "doc_status"=>array ( 1 => '是',0 => '否' ),
	),

    'tpl_fields' => [
        "project" => [
            "f_list" => "id:编号|8%,title:信息名:edit,create_time|toDate='y-m-d':创建时间,status|getStatus2:状态",
            "f_action" => 'status|showStatus=$user[\'id\'],edit:编辑:id,foreverdel:永久删除:id',
            'f_add' => 'title,create_time',
        ],

    ]
	
);




