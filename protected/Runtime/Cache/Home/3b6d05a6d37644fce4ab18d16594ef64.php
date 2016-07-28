<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>接口列表</title>
<script src="http://libs.baidu.com/jquery/1.9.1/jquery.min.js"></script>
<script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
<link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
<style>
#page a{float:left;margin:5px 2px 0 2px;height:30px;color: #000;font:18px 宋体;text-align:center;text-decoration:none;color:#3399ff;border:1px #3399ff solid;background:#fff;line-height:30px;border-radius:4px;}
#page a:hover{position:relative;margin:0 -5px 0 -5px;width:auto;height:40px;font:bold 24px 宋体;color:#3399ff;border:1px #3399ff solid;background:#fff;line-height:40px;border-radius:4px;}
.current{float:left;width:32px;height:32px;color:#fff;background:#33ffff;margin:5px 1px 0 1px;text-align:center;line-height:32px;border-radius:4px;}
#page{list-style:none;width:auto;height:50px;font:18px 宋体;line-height:40px;text-align:center;color:#3399ff;}
</style>
</head>
<body>
<div class="container">
	<h1>全部接口列表</h1>
	<div class="raw">
		<div class="col-md-6">
			<form  class="form-inline" role="form"  method="GET" action="/web/appcli/index.php/Home/Test/index" target="_self">
				<div class="form-group">
					<input type="text" class="form-control" id="title" name="title" placeholder="请输入名称" value="<?php if(empty($_GET['title'])): else: echo ($_GET['title']); endif; ?>">
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="url" name="url" placeholder="请输入url" value="<?php if(empty($_GET['url'])): else: echo ($_GET['url']); endif; ?>">
				</div>
				<div class="form-group">
					<div class="dropdown">
						<button type="button" class="btn dropdown-toggle" id="dropdownMenu1" name="type" data-toggle="dropdown">
							<?php if(empty($_GET['type'])): ?>接口类型<?php else: echo ($_GET['type']); endif; ?>
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
							<?php echo W('Category/Types');?>
						</ul>
					</div> 
					<input type="text" id="type" name="type" class="form-control hidden" value="<?php if(empty($type)): else: echo ($type); endif; ?>">
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-default">提交</button>
				</div>
			</form><br>
		</div>
		<div class="col-md-6 ">
			<a href="/web/appcli/index.php/Home/Test/edit" target="_blank">
				<button type="button" class="btn btn-success">
					<span class="glyphicon glyphicon-plus"></span>  新增接口
				</button>
			</a>
			<a href="/web/appcli/index.php/Home/Test/importHttp" target="_blank">
				<button type="button" class="btn btn-primary">
					<span class="glyphicon glyphicon-cloud"></span>  HTTP接口
				</button>
            </a>
			<a href="/web/appcli/index.php/Home/Test/index?status=-1" target="_blank">
				<button type="button" class="btn btn-danger">
					<span class="glyphicon glyphicon-trash"></span>  回收站
				</button>
			</a>
			<?php if(I('environment') == test) {?>
			<a href="/web/appcli/index.php/Home/Test/index?environment=online">
                <button type="button" class="btn btn-info">
                    <span class="glyphicon glyphicon-star"></span>  线上环境
                </button>
            </a>
            <?php }else{ ?>
            <a href="/web/appcli/index.php/Home/Test/index?environment=test">
                <button type="button" class="btn btn-info">
                    <span class="glyphicon glyphicon-star"></span>  测试环境
                </button>
            </a>
            <?php } ?>
			
		</div>
	</div>
	
	<div class="raw">
		<div class="col-md-12">
			<form class="form-inline">
				<div class="form-group">
	    			<input type="text" class="form-control"  placeholder="更改host:192.168.19.3" >
  				</div>
  				<div  class="btn btn-default" id="hosts">保存</div>
  			</form>
		</div>
	</div>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
                <th>id</th>
                <th>名称</th>
                <th>url</th>
                <th>类型</th>
                <th>操作</th>
                <th>删除或还原</th>
			</tr>
		</thead>  
		<tbody>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                <td><?php echo ($vo["id"]); ?></td>
                <td><?php echo ($vo["title"]); ?></td>
                <td><?php echo ($vo["url"]); ?></td>
                <td><?php echo ($vo["type"]); ?></td>
                <td>
                    <a href="/web/appcli/index.php/Home/Test/edit?id=<?php echo ($vo["id"]); ?>&environment=<?php echo ($_GET['environment']); ?>" target="_self">编辑</a>
                    <a href="/web/appcli/index.php/Home/Test/test/test?id=<?php echo ($vo["id"]); ?>" target="_blank">测试</a>
                    <a href="/web/appcli/index.php/Home/Test/copy?id=<?php echo ($vo["id"]); ?>" target="_blank">复制</a>
                </td>
                <td>
                    <?php if(($vo["status"]) == "-1"): ?><a href="/web/appcli/index.php/Home/Test/delete?id=<?php echo ($vo["id"]); ?>" target="_blank" title="还原">
                            <span class=" glyphicon glyphicon-refresh"></span>
                        </a>
                    <?php else: ?>
                        <a href="/web/appcli/index.php/Home/Test/delete?id=<?php echo ($vo["id"]); ?>" target="_blank"  title="删除">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a><?php endif; ?>
                </td>
                
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>
	<div id="page"><?php echo ($page); ?></div>
</div>
</body>
<script>
  $(".dropdown-menu li a").click(function(){
    var text = $(this).text();
    $("#dropdownMenu1").val(text).text(text);
    if (text != '无'){
    	$("#type").val(text);	
    }
  });
  $(".dropdown-menu li a").click(function(){
	    var text = $(this).text();
	    $("#dropdownMenu1").val(text).text(text);
	    if (text != '无'){
	    	$("#type").val(text);	
	    }
	  });
  $("#hosts").click(function(){
	  var a = $("#hosts").siblings(".form-group");
	  var b= a.find("input").val();
	  $.post("hosts",
	  {
	    host:b,
	  },
	  function(data,status){
		  if(data == -1){
			  alert("IP地址格式错误");
		  }
		  location.reload();
	  });
	});
</script>
</html>