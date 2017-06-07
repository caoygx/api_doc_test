# api_doc_test
api文档管理工具，api接口自动测试工具，代码生成工具

支持搜索，json格式折叠，高亮显示

同时也可以生成control,model,view等基本文件。
可以自己配置controler,model,view的模板，模板文件在 ./Application/Home/View/Form/目录下，可自己修改。
生成的文件在当前目录的data里。




# 安装依赖
依赖thinkphp框架  https://github.com/caoygx/ThinkPHP.git

git clone https://github.com/caoygx/ThinkPHP.git 

将thinkphp框架和当前项目放在同一级目录下如：
/www/thinkphp
/www/api_doc_text


# 使用mysql数据库安装方法

1.将api.sql导入到任意的数据库里，或新建一个数据库

2.更改config里的数据库配置

# 使用sqlite数据库直接下载后打开




3.浏览器里打开 http://localhost/api_doc_test/index.php/Home/Doc/index

![](https://github.com/caoygx/api_doc_test/blob/master/screenshot1.jpg)





# 脚本批量测试
命令
php /www/daoxila/tools/appcli/index.php /home/autotest/bat_api


# 根据表注释，自动实现添加,修改，列表，搜索基本功能

以 |-，之类的做分隔
注释标题 - htm控件类型 - 提示 |展现页面 | 校验类型 |  选项

注释标题: 一般是字段的中文标题，form表单的label
html控件类型: select,checkbox,input,textare,datepicker,editor等
提示:一般是此字段的填写规范，如：允许字母或数字
校验类型:reqiure,email,username,mobile等,用于后台校验,对应thinkphp的校验格式

展现页面:用位表示 
1       1          1       1
添加  修改    列表   搜索项
8       4           2      1
可以用每1位的10进制数相加的和表示，也可以直接用二进制表示
1011(二进制)  等价于 11(十进制)

例:
添加,修改，列表,搜索全显示 1111 = 15
添加,修改，列表都要显示则是  1110 = 14
添加，修改显示，列表不显示    1100 = 10
添加，修改不显示，列表显示，一般像创建时间就是这样  0010 = 1


选项： 选项1:选项1值，选项2：缺项2值
   

状态-select-禁用则不能访问 | 15| reqiure  | 0:禁用,1:正常,2:审核中





# 批量生成表单(旧版).
根据表字段自动生成form表单的，在开发中是不是会遇到几十个表字段要做个form 提交，一个个写表单，并给字段命名，有没有种抓狂的感觉？现在可以用这个工具来帮你生成表单了。
地址: index.php/home/form/index

![](https://github.com/caoygx/api_doc_test/blob/master/generate.jpg)



详细说明：
使用自动生成表单时，定义数据的格式如下
`status` int(11) DEFAULT NULL COMMENT '状态-select-提示内容|0:禁用,1:正常,2:待审核|7',

'中文标题-输入输入框类型-提示内容|选项1key:选项1值,选项2key:选项2值|标识是哪里展示，4添加，2修改,1列表,类似于linux的权限 755之类的如7表示列表，添加，修改都显示,1表示只在列表显示 '

select 也可以换成checkbox,radio等。



在某个字段有多个值时，在web一般用select展示，可以用下面的规则命名注释来达到自动生成select控件。
 
 

这样可以通过代码生成工具通过","和"|",":"来分隔，然后获取到这个字段的中文名，作为表单的label,选项文本作为select的text,选项key作为select的key
 
 ```
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
``` 
 


#功能3.
代码生成功能
![](https://github.com/caoygx/api_doc_test/blob/master/code_generate.jpg)


 
