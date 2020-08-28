<?php
if(IS_CLI){
	define('LF',"\n");
}else{
	define('LF',"<br />");
}

define('URL_API','http://pm.'.DOMAIN.'/index.php');
define('DB_TYPE','mysql');
$parentDir = dirname(ROOT);
//var_dump( getenv('ENV_PATH'));exit('x');

if(DB_TYPE == 'mysql'){
    $custom = [
        'DB_TYPE' => "mysql",
        'DB_NAME' => 'doc',
        'DB_HOST' => "localhost",
        'DB_USER' => "root",
        'DB_PWD'  => "123456",
        'DB_PREFIX' => '',
        'DB_PARAMS'    =>    array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),

        //项目数据库连接
        'project_db_config' =>[
            'DB_TYPE' => "mysql",
            'DB_NAME' => 'tesuo',
            'DB_HOST' => "localhost",
            'DB_USER' => "root",
            'DB_PWD'  => "123456",
            'DB_PREFIX' => 'cgf_',
            'DB_PARAMS'    =>    array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),
        ]
    ];
}else{
    $custom = [
        'DB_TYPE'    => 'sqlite',
        'DB_NAME'    => ROOT.'/doc.db',
        'DB_PREFIX' => '',
    ];
}
$pub =  array(
    'test_proxy'=>"192.168.16.16:8888",
    'SHOW_PAGE_TRACE' => true,
    'TAGLIB_PRE_LOAD' => 'html', //,OT\\TagLib\\Think
    'URL_MODEL'=>2, //默认1;URL模式：0 普通模式 1 PATHINFO 2 REWRITE 3 兼容模式
	//'LOG_RECORD'=>true, 

	'service_type' =>  array( '全局' , '通用' , '助手' ),

    'options' => array(
		"bug_status"=>array ( 1 => '已收单', 2 => '已分级', 3 => '已分配', 4 => '已定位', 5 => '解决中', 6 => '已解决', 7 => '已上线', 8 => '已完结', 9 => '不是bug', ),
        "doc_status"=>array ( 1 => '是',0 => '否' ),
	),

    //默认操作
    "f_action" => 'status|showStatus=$user[\'id\'],edit:编辑:id,foreverdel:永久删除:id',
    'tpl_fields' => [
        "project" => [
            "f_list" => "id:编号|8%,title:信息名:edit,create_time|toDate='y-m-d':创建时间,status|getStatus2:状态",
            "f_action" => 'status|showStatus=$user[\'id\'],edit:编辑:id,foreverdel:永久删除:id',
            'f_add' => 'title,create_time',
        ],

    ]
	
);

return array_merge($pub,$custom);



