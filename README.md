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

3.启动go服务   
   3.1 go run api.go  用于获取表字段  
   3.2 main.go中用于自动生成接口文档 具体步骤见main.go中的注释  




4.浏览器里打开 http://localhost/api_doc_test/index.php/Home/Doc/index

![](https://github.com/caoygx/api_doc_test/blob/master/screenshot1.jpg)


# json格式测试
1.类型验证
2.非空验证
3.指定的精确值验证
4.包含指定值验证

{
    "isNew": "f:type:bool|f:value:true",
    "totalEarn": 0,
    "bindPhone": "",
    "isBind": false,
    "state": "f:reqire",
    "data": {
        "openid": "sj_fa6af9713bc02a6107785463",
        "nickName": "138****6111",
        "head": "f:reg:/url/", //,http://pm.yz314.com/front/static/img/usercenter/photo.png
        "phone": "f:reg:/mobile/",
        "address": "",
        "cname": "",
        "area": "四川省 雅安",
        "balance": 0,
        "memberno": "9856523940",
        "channel_id": "Ios2",
        "isNew": true,
        "totalEarn": 0,
        "bindPhone": "",
        "isBind": false,
        "shareCode": ""
    },
    "msg": "",
    "openid": "sj_fa6af9713bc02a6107785463"
}

f:开头表示格式验证
  type 验证类型,bool,int,string,array
  value 验证值
  value_fuzzy 模糊验证值



# 脚本批量测试
命令
php /www/daoxila/tools/appcli/index.php /home/autotest/bat_api







# 批量生成表单(旧版).
根据表字段自动生成form表单的，在开发中是不是会遇到几十个表字段要做个form 提交，一个个写表单，并给字段命名，有没有种抓狂的感觉？现在可以用这个工具来帮你生成表单了。
地址: index.php/home/form/index

![](https://github.com/caoygx/api_doc_test/blob/master/generate.jpg)




#功能3.
代码生成功能
![](https://github.com/caoygx/api_doc_test/blob/master/code_generate.jpg)





 
