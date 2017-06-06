<?php
namespace Home\Controller;
use Common\CommonController;

class Project2Controller extends CommonController {
	protected $nodeId = [];	
	function add(){
	   $str =  '<!--<form class="form-horizontal" role="form"  method="post" action="/api_doc_test/Home/Project/save/"> -->
	<form class="form-horizontal" role="form"  method="post" action="__URL__/save/">  <div class="form-group">
    <label for="title" class="col-sm-2 control-label">标题</label>
    <div class="col-sm-10">
       <input name="title" type="text" id="title" size="30" value="{$vo.title}" /> 提示:    </div>
  </div>  <div class="form-group">
    <label for="domain" class="col-sm-2 control-label">域名</label>
    <div class="col-sm-10">
       <input name="domain" type="text" id="domain" size="30" value="{$vo.domain}" /> 提示:    </div>
  </div>  <div class="form-group">
    <label for="status" class="col-sm-2 control-label">状态</label>
    <div class="col-sm-10">
       <html:select options=\'opt_status\' selected=\'status_selected\' name="status" /> 提示:提示内容    </div>
  </div>  <div class="form-group">
    <label for="create_time" class="col-sm-2 control-label">创建时间</label>
    <div class="col-sm-10">
       <input name="create_time" type="text" id="create_time" size="30" value="{$vo.create_time}" /> 提示:    </div>
  </div>  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default">保存</button>
    </div>
  </div>
</form>';


        $project_status =array (
            0 => '禁用',
            1 => '正常',
            2 => '待审核',
        );

        $this->opt_status = $project_status;
       // $this->assign('opt_status',$project_status);
        $r = $this->fetch('',$str);
        var_dump($r);exit('x');
//	    $this->display();
    }

}




