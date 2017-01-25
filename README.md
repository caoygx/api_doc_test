# api_doc_test
api文档管理工具，api接口自动测试工具，代码生成工具

支持搜索，json格式折叠，高亮显示

同时也可以生成control,model,view等基本文件。
可以自己配置controler,model,view的模板，模板文件在 ./Application/Home/View/Form/目录下，可自己修改。
生成的文件在当前目录的data里。




安装方法

1.将api.sql导入到任意的数据库里，或新建一个数据库

2.更改config里的数据库配置

3.浏览器里打开 http://localhost/api_doc_test/index.php/Home/Doc/index

![](https://github.com/caoygx/api_doc_test/blob/master/screenshot1.jpg)





#功能1.
脚本批量测试命令
php /www/daoxila/tools/appcli/index.php /home/autotest/bat_api



#功能2.
根据表字段自动生成form表单的，在开发中是不是会遇到几十个表字段要做个form 提交，一个个写表单，并给字段命名，有没有种抓狂的感觉？现在可以用这个工具来帮你生成表单了。
地址: index.php/home/form/index

![](https://github.com/caoygx/api_doc_test/blob/master/generate.jpg)



详细说明：
使用自动生成表单时，定义数据的格式如下
`status` int(11) DEFAULT NULL COMMENT '状态-select-提示内容|0:禁用,1:正常,2:待审核',

select 也可以换成checkbox,radio等。



在某个字段有多个值时，在web一般用select展示，可以用下面的规则命名注释来达到自动生成select控件。
 

 COMMENT '字段中文名，选项1的key:选项1的文本|选项2的key:选项2的text' 

 
`status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态，0:禁用|1:正常|2:待审核'
 

这样可以通过代码生成工具通过","和"|",":"来分隔，然后获取到这个字段的中文名，作为表单的label,选项文本作为select的text,选项key作为select的key
 
<?php
$id=$name='status';

$commnet = '状态,0:禁用|1:正常|2:待审核';

$arr = explode(",",$commnet);

$label = "<label>{$arr[0]}</label>";

$items = explode("|",$arr[1]);

$options = "";

foreach($items as $item){

list($value, $text) = explode(':',$item);

$options .= "$text";

}

$select = "<select id="$id" name="$name">$options

</select>";

$form_row = $label.$select;

echo $form_row; 




运行后会生成如下表单内容
<label>状态</label><select id="status" name="status">
<option value="0">禁用</option>
<option value="1">正常</option>
<option value="2">待审核</option>
</select>
 
 


#功能3.
代码生成功能
![](https://github.com/caoygx/api_doc_test/blob/master/code_generate.jpg)

 