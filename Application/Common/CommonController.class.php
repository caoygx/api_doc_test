<?php
namespace Common;
use Think\Controller;
use Org\Util\Rbac;
use Org\Util\Cookie;
use Common\XPage;
use Org\Util\TableInfo;
class CommonController extends Controller {
    /**
     * @var \Model|null|\Think\Model
     */
    protected $m = null;
    protected $user = array(); //用户信息数组
    protected $uid = 0; //用户uid
    protected $u = null;
    protected $autoInstantiateModel = true;
    protected $tempStorageOpenidUser = []; //微信等第三方登录的用户信息临时存储
    protected $isMobile;
    public function __construct($pre = ''){
        parent::__construct();
        if($pre){ //有表前缀
            new CommonModel(CONTROLLER_NAME,$pre);
        }else{
            try{
                //检测表存在，则实例化
                $model = M();
                $tablename = strtolower(C('DB_PREFIX').parse_name(CONTROLLER_NAME));
                if(DB_TYPE == 'mysql'){
                    $sqlCheckTable = "SELECT * FROM information_schema.tables WHERE table_name = '$tablename'"; //mysql
                }else{
                    $sqlCheckTable = "select * from sqlite_master where name='$tablename'";
                }
                $tableExist = $model->query($sqlCheckTable);
                debug($tableExist);
                if($this->autoInstantiateModel && !empty($tableExist)){
                    //if(class_exists(CONTROLLER_NAME.'Model',true)){
                    $this->m = D(CONTROLLER_NAME); //实例化model
                    //}
                    if(empty($this->m)){
                        $this->m = M(CONTROLLER_NAME);
                    }
                }
            }catch(Exception $e){
                //echo $e->getMessage();
                //
            }
        }


        //简单的权限验证操作
        if (method_exists ( $this, '_permissionAuthentication' )) {
            $this->_permissionAuthentication ();
        }

        //if(empty($this->m)) exit(CONTROLLER_NAME.'对象不存在');

    }


    function _initialize() {
        //if(!IS_CLI)	$this->requestLog();

        return;
        //url带openid 自动写cookie,session等登录标识
        $open_id = I('open_id');
        if($open_id){
            $r =  M('User')->where(["open_id" => $open_id,"type" => I('type')])->find();
            if(!empty($r)){
                $r['uid'] = $r['id'];
                $this->tempStorageOpenidUser = $r;
                setUserAuth($r); //登录前在getAuth 里增加个标识，如果get open_id有值，不必取cookie,直接标识为登录
            }
        }

        $this->getAuth();
        //exit('x');exit;
        //用户信息
        if(!empty($this->uid)){
            $m = M('User');
            $r = $m->find($this->uid);
            //var_dump($r);
            //echo $m->getLastSql();
            //exit;
            if(empty($r['nickname']) && !empty($r['username'])) $r['nickname'] = $r['username'];
            $this->assign ( 'user', $r );
        }
        /*
                if(C('USER_AUTH_ON')){
                    import ( '@.ORG.Util.RBAC_WEB' );
                    $app = 'USER';

                }else{
                    import ( 'ORG.Util.RBAC' );
                    $app = APP_NAME;
                }*/
    }


    public function index() {

        //自动获取通用模板时 获取表字段
        $fieldsKey = 'tpl_fields.'.strtolower(CONTROLLER_NAME);
        $tpl_fields = C($fieldsKey);
        if(!empty($tpl_fields)){
            foreach ($tpl_fields as $k => $v){
                //var_dump($k,$v);
                $this->assign($k,$v);
            }
        }
//        var_dump($tpl_fields);exit;


        //列表过滤器，生成查询Map对象
        $map = $this->_search ();
        if (method_exists ( $this, '_filter' )) {
            $this->_filter ( $map );
        }


        $name=CONTROLLER_NAME;
        //$model = D ($name);
        if (! empty ( $this->m )) {
            $this->_list ( $this->m, $map,$this->sortBy );
        }
        $this->toview ();
        return;
    }

    public function msg($result,$text = '',$url=''){
        if(false !== $result){
            $this->success($text."成功");
        }else{
            $this->error($text."失败");
        }
    }

    //获取用户登录凭证信息
    function getAuth(){
        $u = getUserAuth();
        if(CONF_ENV=='dev'){
            //$u['uid'] = 4;
        }
        if(empty($u) && !empty($this->tempStorageOpenidUser)) $u = $this->tempStorageOpenidUser;
        $this->user = $u;
        $this->uid = $this->user['uid'];
        return $u;
    }


    public $logId;
    /**
     * 访问日志，记录用户请求的参数
     */
    function requestLog(){


        $data = array();
        $data['url'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if(IS_POST){
            $params = $_POST;
        }elseif(IS_GET){
            $params = $_GET;
        }
        if(empty($params)) $params['input'] = file_get_contents("php://input");
        $data['params'] = json_encode($params);
        //$data['cookie'] = json_encode($_COOKIE);
        //$data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $data['ip'] = get_client_ip();
        $detail = array();
        $detail['request'] = $_REQUEST;

        $header = [];
        $fields = ['HTTP_USER_ID','HTTP_DEVICE_VID','HTTP_DEVICE_ID','HTTP_PLATFORM','HTTP_VERSION']; //'HTTP_USER_AGENT',
        foreach ($fields as $k => $v){
            if(empty($_SERVER[$v])) continue;
            $header[$v] = $_SERVER[$v];
        }
        /*$this->version = I('server.HTTP_VERSION');
        $this->device_id = I('device_id') ?:I('server.HTTP_DEVICE_ID');
        $this->platform = I('server.HTTP_PLATFORM');
        $user_id = I('user_id') ?: I('server.HTTP_USER_ID');
        $detail['server'] = $_SERVER;*/
        //$detail['header'] = $header;
        //$data['detail'] = json_encode($detail);
        $url = $_SERVER['REQUEST_METHOD']." ".$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']." ".$_SERVER['SERVER_PROTOCOL']."\r\n";
        $request = $url.getallheaders(true);

        $raw_post = '';
        if(IS_POST){
            $raw_post = http_build_query($_POST);
            if(empty($raw_post)){
                $raw_post = file_get_contents("php://input");
            }
        }
        $request .= "\r\n".$raw_post;

        $data['detail'] = $request;
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $data['platform'] = I('server.HTTP_PLATFORM');
        $data['user_id'] = I('server.HTTP_USER_ID');
        $data['create_time'] = date("Y-m-d H:i:s");
        $data['method'] = $_SERVER['REQUEST_METHOD'];
        $data['date_int'] = time();
        //$m = M('LogRequest');
        $m = D('RequestLog');
        //$m->create($data);
        $this->logId = $m->add($data);
        //echo $m->getLastSql();exit;

    }

    function responseLog($id,$response){
        $data = [];
        $data['id'] = $id;
        $data['response'] = $response;
        //$m = M('LogRequest');
        $m = D('RequestLog');
        $m->save($data);

    }

    public function lists() {

        //列表过滤器，生成查询Map对象
        $map = $this->_search ();
        if (method_exists ( $this, '_filter' )) {
            $this->_filter ( $map );
        }
        $name=CONTROLLER_NAME;
        if (! empty ( $this->m )) {
            $this->_list ( $this->m, $map );
        }


        //自动获取列表模板
        layout(false);
        $tableInfo = new TableInfo('list');
        $tableName = $this->m->getTableName();
        $htmlList = $tableInfo->generateLists($tableName);

//        $tableInfo->page = 'search';
//        $htmlSearch = $tableInfo->generateSearch($tableName);
//        var_dump($htmlSearch);exit;


//        <input name="id" type="text" class="input-medium search-query" <literal>value="{$Think.get.id}"</literal> placeholder="id">
//		      <input name="title" type="text" class="input-medium search-query" <literal>value="{$Think.get.title}"</literal>   placeholder="名称">

        $this->htmlList = $htmlList;

        $this->display ();
        //return;
    }

    //有连接表显示列表
    public function indexLink($option=array()) {
        //列表过滤器，生成查询Map对象
        $map = $this->_search ();
        if (method_exists ( $this, '_filter' )) {
            $this->_filter ( $map );
        }
        $name=CONTROLLER_NAME;
        //$model = D ($name);
        if (! empty ( $this->m )) {
            if($option['join']){
                $this->_listLink ( $this->m, $map,$option );
            }else{
                $this->_list($this->m,$map);
            }
        }
        $this->display ();
        return;
    }

    /**
    +----------------------------------------------------------
     * 取得操作成功后要返回的URL地址
     * 默认返回当前模块的默认操作
     * 可以在action控制器中重载
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     * @throws ThinkExecption
    +----------------------------------------------------------
     */
    function getReturnUrl() {
        return __CONTROLLER__ . '/'  .   C ( 'DEFAULT_ACTION' );
    }

    /**
    +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
    +----------------------------------------------------------
     * @access protected
    +----------------------------------------------------------
     * @param string $name 数据对象名称
    +----------------------------------------------------------
     * @return HashMap
    +----------------------------------------------------------
     * @throws ThinkExecption
    +----------------------------------------------------------
     */
    protected function _search($name = '') {
        //生成查询条件
        if (empty ( $name )) {
            $name = CONTROLLER_NAME;
        }
        //$name=CONTROLLER_NAME;
        //$model = D ( $name );
        //var_dump($this->m);exit;
        $map = array ();
        foreach ( $this->m->getDbFields () as $key => $val ) {
            if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
                $map [$val] = trim($_REQUEST [$val]);
            }
        }
        return $map;

    }

    /**
    +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
    +----------------------------------------------------------
     * @access protected
    +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
    +----------------------------------------------------------
     * @return void
    +----------------------------------------------------------
     * @throws ThinkExecption
    +----------------------------------------------------------
     */
    protected function _list($model, $map, $sortBy = '', $asc = false) {
        //排序字段 默认为主键名
        if (isset ( $_REQUEST ['_order'] )) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        //$setOrder = setOrder(array(array('viewCount', 'a.view_count'), 'a.id'), $orderBy, $orderType, 'a');
        if (isset ( $_REQUEST ['_sort'] )) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
        //取得满足条件的记录数
        $pk = $model->getPk();
        $count = $model->where ( $map )->count ( $pk );//echo $model->getlastsql();exit('count');
        if ($count > 0) {
            import ( "ORG.Util.Page" );
            //创建分页对象
            if (! empty ( $_REQUEST ['listRows'] )) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '10';
            }
            $p = new \Think\Page ( $count, $listRows );
            $p->rollPage = 7;
            //echo C('PAGE_STYLE');exit;
            $p->style = C('PAGE_STYLE');//设置风格
            //分页查询数据
            //var_dump($p->listRows);exit;
            $voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
            if (method_exists ( $this, '_join' )) {
                $this->_join ( $voList );
            }
            //echo $model->getlastsql();exit('x');
            //分页跳转的时候保证查询条件
            foreach ( $map as $key => $val ) {
                if (! is_array ( $val )) {
                    $p->parameter .= "$key=" . urlencode ( $val ) . "&";
                }
            }
            //分页显示
            $page = $p->show ();
            //列表排序显示
            $sortImg = $sort == 'desc' ? 'glyphicon-arrow-down' : "glyphicon-arrow-up";
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign ( 'list', $voList );
            $this->assign ( 'sort', $sort );
            $this->assign ( 'order', $order );
            $this->assign ( 'sortImg', $sortImg );
            $this->assign ( 'sortType', $sortAlt );
            $this->assign ( "page", $page );
        }
        cookie( '_currentUrl_', __SELF__ );
        return;
    }

    /**
    +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
    +----------------------------------------------------------
     * @access protected
    +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
    +----------------------------------------------------------
     * @return void
    +----------------------------------------------------------
     * @throws ThinkExecption
    +----------------------------------------------------------
     */
    protected function _list2($model, $map, $sortBy = '', $asc = false) {
        //排序字段 默认为主键名
        if (isset ( $_REQUEST ['_order'] )) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        //$setOrder = setOrder(array(array('viewCount', 'a.view_count'), 'a.id'), $orderBy, $orderType, 'a');
        if (isset ( $_REQUEST ['_sort'] )) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
        //取得满足条件的记录数
        $pk = $model->getPk();
        $count = $model->where ( $map )->count ( $pk );
        //echo $model->getlastsql();exit('count');
        $ret = array();
        if ($count > 0) {
            import ( "ORG.Util.Page" );
            //创建分页对象
            if (! empty ( $_REQUEST ['listRows'] )) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = 10;
            }
            $p = new \Think\PageJs ( $count, $listRows );
            //echo C('PAGE_STYLE');exit;
            $p->style = C('PAGE_STYLE');//设置风格
            //分页查询数据
            //var_dump($p);exit;
            $voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
            if (method_exists ( $this, '_join' )) {
                $this->_join ( $voList );
            }
            //var_dump($voList);exit;
            //echo $model->getlastsql();exit('x');
            //分页跳转的时候保证查询条件
            foreach ( $map as $key => $val ) {
                if (! is_array ( $val )) {
                    /*$p->parameter = http_build_query($p->parameter);
                    $p->parameter .= "$key=" . urlencode ( $val ) . "&";*/
                }
            }
            //分页显示
            $page = $p->show ();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式

            $ret['list'] = $voList;

            /* if($isweb){
                $ret['sort'] = $sort;
                $ret['order'] = $order;
                $ret["page"] = $page ;
            } */
            //模板赋值显示
            /*  $this->assign ( 'list', $voList );
             $this->assign ( 'sort', $sort );
             $this->assign ( 'order', $order );
             $this->assign ( 'sortImg', $sortImg );
             $this->assign ( 'sortType', $sortAlt );*/

        }
        // echo $model->getLastSql();
        $this->success($ret);
    }


    /**
    +----------------------------------------------------------
     * 根据表单生成查询条件
     * 进行列表过滤
     * 返回结果，不输出
    +----------------------------------------------------------
     * @access protected
    +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
    +----------------------------------------------------------
     * @return void
    +----------------------------------------------------------
     * @throws ThinkExecption
    +----------------------------------------------------------
     */
    protected function _getlist($model, $map, $sortBy = '', $asc = false) {
        //排序字段 默认为主键名
        if (isset ( $_REQUEST ['_order'] )) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        //$setOrder = setOrder(array(array('viewCount', 'a.view_count'), 'a.id'), $orderBy, $orderType, 'a');
        if (isset ( $_REQUEST ['_sort'] )) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }
        //取得满足条件的记录数
        $pk = $model->getPk();
        $count = $model->where ( $map )->count ( $pk );
        if ($count > 0) {
            import ( "ORG.Util.Page" );
            //创建分页对象
            if (! empty ( $_REQUEST ['listRows'] )) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new Page ( $count, $listRows );
            //echo C('PAGE_STYLE');exit;
            $p->style = C('PAGE_STYLE');//设置风格
            //分页查询数据
            //var_dump($p->listRows);exit;
            $voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
            //echo $model->getlastsql();
            //分页跳转的时候保证查询条件
            foreach ( $map as $key => $val ) {
                if (! is_array ( $val )) {
                    $p->parameter .= "$key=" . urlencode ( $val ) . "&";
                }
            }
            //分页显示
            $page = $p->show ();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            return array('list' => $voList ,
                'sort' => $sort,
                'order' => $order);
            //$this->assign ( 'sortImg', $sortImg );
            //$this->assign ( 'sortType', $sortAlt );
            //$this->assign ( "page", $page );*
            /*$this->assign ( 'list', $voList );
            $this->assign ( 'sort', $sort );
            $this->assign ( 'order', $order );
            $this->assign ( 'sortImg', $sortImg );
            $this->assign ( 'sortType', $sortAlt );
            $this->assign ( "page", $page );*/
        }
        cookie( '_currentUrl_', __SELF__ );
        return;
    }

    /**
    +----------------------------------------------------------
     * 连接查询列表显示
     * 进行列表过滤
    +----------------------------------------------------------
     * @access protected
    +----------------------------------------------------------
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
    +----------------------------------------------------------
     * @return void
    +----------------------------------------------------------
     * @throws ThinkExecption
    +----------------------------------------------------------
     */
    protected function _listLink($model,$map,$option=array(),  $sortBy = '', $asc = false) {

        extract($option);

        $field || $field = "*";
        $table || $table = $model->getTableName();
        //$table = "{$this->trueTableName} j";
        //$r = $this->table($table)->field($field)->join($join)->where($map)->count();

        //dump($r);
        //return $r;

        //排序字段 默认为主键名
        if (isset ( $_REQUEST ['_order'] )) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        //$setOrder = setOrder(array(array('viewCount', 'a.view_count'), 'a.id'), $orderBy, $orderType, 'a');
        if (isset ( $_REQUEST ['_sort'] )) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }

        //取得满足条件的记录数
        if($sql){
            $sqlCount = getCountSql($sql);
            //处理map查询条件
            $count = $db->query($sqlCount);
        }else{
            $pk = $model->getPk();
            $count = $model->table($table)->field($field)->join($join)->where($map)->count( $pk );
        }
        if ($count > 0) {
            import ( "ORG.Util.XPage" );
            //创建分页对象
            if (! empty ( $_REQUEST ['listRows'] )) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new XPage ( $count, $listRows );
            //echo C('PAGE_STYLE');exit;
            //$s =  rand(1,25);echo $s;
            $p->style = C('PAGE_STYLE');//设置风格
            //分页查询数据
            if ($sql) {
                //处理map查询条件
                $voList = $model->query(sql. "`" . $order . "` " . $sort.$p->firstRow . ',' . $p->listRows);
            }else{
                $voList = $model->table($table)->field($field)->join($join)->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
            }
            //echo $model->getlastsql();


            //高亮关键字
            if(C('highLightKeyword') && $_REQUEST['keyword']){
                $keyword = $_REQUEST['keyword'];
                foreach($voList as $k => $v){
                    $voList[$k]['jtitle'] = hightLightKeyword($v['jtitle'],$keyword);
                    $voList[$k]['request'] = hightLightKeyword($v['request'],$keyword);
                    $voList[$k]['ctitle'] = hightLightKeyword($v['ctitle'],$keyword);
                }
            }
            //分页跳转的时候保证查询条件
            foreach ( $map as $key => $val ) {
                if (! is_array ( $val )) {
                    $p->parameter .= "$key=" . urlencode ( $val ) . "&";
                }
            }
            //分页显示
            $page = $p->show ();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign ( 'list', $voList );
            $this->assign ( 'sort', $sort );
            $this->assign ( 'order', $order );
            $this->assign ( 'sortImg', $sortImg );
            $this->assign ( 'sortType', $sortAlt );
            $this->assign ( "page", $page );
        }
        cookie( '_currentUrl_', __SELF__ );
        return;
    }

    function advancedList($model,$map,$join,$field="*",$table="",  $sortBy = '', $asc = false) {

        $option['join'] = $join; //有查询条件，开启连接查询

        $field || $field = "*";
        $table || $table = $model->getTableName();
        //$table = "{$this->trueTableName} j";
        //$r = $this->table($table)->field($field)->join($join)->where($map)->count();

        //dump($r);
        //return $r;

        //排序字段 默认为主键名
        if (isset ( $_REQUEST ['_order'] )) {
            $order = $_REQUEST ['_order'];
        } else {
            $order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
        }
        //排序方式默认按照倒序排列
        //接受 sost参数 0 表示倒序 非0都 表示正序
        //$setOrder = setOrder(array(array('viewCount', 'a.view_count'), 'a.id'), $orderBy, $orderType, 'a');
        if (isset ( $_REQUEST ['_sort'] )) {
            $sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
        } else {
            $sort = $asc ? 'asc' : 'desc';
        }

        //取得满足条件的记录数
        $pk = $model->getPk();

        if ($option['num'] ){ //限制取几条记录，直接返回指定条记录
            if($sql){
                $voList = $model->query($sql);
            }elseif($option['join']){
                $voList = $model->table($table)->field($field)->join($join)->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
            }else{
                $voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
            }
            return $voList;
        }else{ //分页

            if($sql){
                $count = $count = $model->query(getCountSql($sql));
                $count = $count[0];
            }elseif($option['join']){
                $count = $model->table($table)->field($field)->join($join)->where($map)->count( $pk );

            }else{
                $count = $model->where ( $map )->count ( $pk );
            }
            if($count<0) return;
            import ( "ORG.Util.XPage" );
            //创建分页对象
            if (! empty ( $_REQUEST ['listRows'] )) {
                $listRows = $_REQUEST ['listRows'];
            } else {
                $listRows = '';
            }
            $p = new XPage ( $count, $listRows );
            //echo C('PAGE_STYLE');exit;
            //$s =  rand(1,25);echo $s;
            $p->style = C('PAGE_STYLE');//设置风格
            //分页查询数据

            if($sql){
                $voList = $model->query($sql);
            }elseif($option['join']){
                $voList = $model->table($table)->field($field)->join($join)->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
            }else{
                $voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
            }

            //高亮关键字
            if(C('highLightKeyword') && $_REQUEST['keyword']){
                $keyword = $_REQUEST['keyword'];
                foreach($voList as $k => $v){
                    $voList[$k]['jtitle'] = hightLightKeyword($v['jtitle'],$keyword);
                    $voList[$k]['request'] = hightLightKeyword($v['request'],$keyword);
                    $voList[$k]['ctitle'] = hightLightKeyword($v['ctitle'],$keyword);
                }
            }
            //分页跳转的时候保证查询条件
            foreach ( $map as $key => $val ) {
                if (! is_array ( $val )) {
                    $p->parameter .= "$key=" . urlencode ( $val ) . "&";
                }
            }
            //分页显示
            $page = $p->show ();
            //列表排序显示
            $sortImg = $sort; //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
            $sort = $sort == 'desc' ? 1 : 0; //排序方式
            //模板赋值显示
            $this->assign ( 'list', $voList );
            $this->assign ( 'sort', $sort );
            $this->assign ( 'order', $order );
            $this->assign ( 'sortImg', $sortImg );
            $this->assign ( 'sortType', $sortAlt );
            $this->assign ( "page", $page );
        }
        cookie( '_currentUrl_', __SELF__ );
        return;
    }

    function insert() {
        //B('FilterString');
        $name=CONTROLLER_NAME;
        //$model = D ($name);
        if (false === $this->m->create ()) {
            $this->error ( $this->m->getError () );
        }
        //保存当前数据对象
        $list=$this->m->add ();
        //echo $this->m->getLastSql();exit;
        if ($list!==false) { //保存成功
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
            $this->success ('新增成功!');
        } else {
            //失败提示
            $this->error ('新增失败!');
        }
    }

    public function add() {
        if (method_exists ( $this, '_replacePublic' )) {
            $this->_replacePublic ( $vo );
        }

        //自动获取添加模板
        layout(false);
        $tableInfo = new TableInfo('add');
        $tableName = $this->m->getTableName();
        $form = $tableInfo->generateForm($tableName);

        /*//自动获取通用模板时 获取表字段
        $fields = $this->m->getDbFields();
        $pk = $this->m->getPk();
        $tableInfo = [];
        foreach ($fields as $k =>$v){
            if($v == $pk){
                unset($fields[$k]);
                continue;
            }
            $tableInfo[] = ["column_name" => $v,"cn_name" => $v];
        }
        $this->assign('f_add',$tableInfo);*/
        //$this->assign('action','add');
        $this->form = $form;
        $this->toview();
    }

    function read() {
        $this->edit ();
    }

    function edit() {

        $id = $_REQUEST [$this->m->getPk ()];
        $vo = $this->m->getById ( $id );
        if (method_exists ( $this, '_replacePublic' )) {
            $this->_replacePublic ( $vo );
        }
        //cookie( '_currentUrl_', __SELF__ );
        $this->vo = $vo;
        $this -> assign('action','edit');

        //自动获取添加模板
        layout(false);
        $tableInfo = new TableInfo('edit');
        $tableName = $this->m->getTableName();
        $form = $tableInfo->generateForm($tableName);
        $this->form = $form;

        $this->toview("","add");
    }

    function edit2() {
        $id = $_REQUEST [$this->m->getPk ()];
        $vo = $this->m->getById ( $id );
        if (method_exists ( $this, '_replacePublic' )) {
            $this->_replacePublic ( $vo );
        }
        //var_dump($vo);
        //exit('x');
        $this->success($vo);

        //$this->assign ( 'vo', $vo );
        //$this->display ('add');
    }

    function update() {
        //B('FilterString');
        $name=CONTROLLER_NAME;
        //$model = D ( $name );
        if (false === $this->m->create ()) {
            $this->error ( $this->m->getError () );
        }
        // 更新数据
        $list=$this->m->save ();
        if (false !== $list) {
            //成功提示
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
            $this->success ('编辑成功!');
        } else {
            //错误提示
            $this->error ('编辑失败!');
        }
    }
    /**
    +----------------------------------------------------------
     * 默认删除操作
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     * @throws ThinkExecption
    +----------------------------------------------------------
     */
    public function delete() {
        //删除指定记录
        $name=CONTROLLER_NAME;
        //$model = M ($name);
        if (! empty ( $this->m )) {
            $pk = $this->m->getPk ();
            $id = $_REQUEST [$pk];
            if (isset ( $id )) {
                $condition = array ($pk => array ('in', explode ( ',', $id ) ) );
                $list=$this->m->where ( $condition )->setField ( 'status', - 1 );
                if ($list!==false) {
                    $this->success ('删除成功！',cookie ( '_currentUrl_' ));
                } else {
                    $this->error ('删除失败！');
                }
            } else {
                $this->error ( '非法操作' );
            }
        }
    }
    public function foreverdelete() {
        //删除指定记录
        $name=CONTROLLER_NAME;
        //$model = D ($name);
        if (! empty ( $this->m )) {
            $pk = $this->m->getPk ();
            $id = $_REQUEST [$pk];
            if (isset ( $id )) {
                $condition = array ($pk => array ('in', explode ( ',', $id ) ) );
                if (false !== $this->m->where ( $condition )->delete ()) {
                    //echo $this->m->getlastsql()
                    $this->assign ( 'jumpUrl', cookie ( '_currentUrl_' ) );
                    $this->success ('删除成功！',cookie ( '_currentUrl_' ));
                } else {
                    $this->error ('删除失败！');
                }
            } else {
                $this->error ( '非法操作' );
            }
        }
        $this->forward ();
    }

    public function clear() {
        //删除指定记录
        $name=CONTROLLER_NAME;
        //$this->m = D ($name);
        if (! empty ( $this->m )) {
            if (false !== $this->m->where ( 'status=1' )->delete ()) {
                $this->assign ( "jumpUrl", $this->getReturnUrl () );
                $this->success ( L ( '_DELETE_SUCCESS_' ) );
            } else {
                $this->error ( L ( '_DELETE_FAIL_' ) );
            }
        }
        $this->forward ();
    }
    /**
    +----------------------------------------------------------
     * 默认禁用操作
     *
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     * @throws FcsException
    +----------------------------------------------------------
     */
    public function forbid() {
        $name=CONTROLLER_NAME;
        //$model = D ($name);
        $pk = $this->m->getPk ();
        $id = $_REQUEST [$pk];
        $condition = array ($pk => array ('in', $id ) );
        $list=$this->m->forbid ( $condition );
        if ($list!==false) {
            $this->assign ( "jumpUrl", $this->getReturnUrl () );
            $this->success ( '状态禁用成功' );
        } else {
            $this->error  (  '状态禁用失败！' );
        }
    }
    public function checkPass() {
        $name=CONTROLLER_NAME;
        //$model = D ($name);
        $pk = $this->m->getPk ();
        $id = $_GET [$pk];
        $condition = array ($pk => array ('in', $id ) );
        if (false !== $this->m->checkPass( $condition )) {
            $this->assign ( "jumpUrl", $this->getReturnUrl () );
            $this->success ( '状态批准成功！' );
        } else {
            $this->error  (  '状态批准失败！' );
        }
    }

    public function recycle() {
        $name=CONTROLLER_NAME;
        //$model = D ($name);
        $pk = $this->m->getPk ();
        $id = $_GET [$pk];
        $condition = array ($pk => array ('in', $id ) );
        if (false !== $this->m->recycle ( $condition )) {

            $this->assign ( "jumpUrl", $this->getReturnUrl () );
            $this->success ( '状态还原成功！' );

        } else {
            $this->error   (  '状态还原失败！' );
        }
    }

    public function recycleBin() {
        $map = $this->_search ();
        $map ['status'] = - 1;
        $name=CONTROLLER_NAME;
        //$model = D ($name);
        if (! empty ( $this->m )) {
            $this->_list ( $this->m, $map );
        }
        $this->display ();
    }

    /**
    +----------------------------------------------------------
     * 默认恢复操作
     *
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @return string
    +----------------------------------------------------------
     * @throws FcsException
    +----------------------------------------------------------
     */
    function resume() {

        //恢复指定记录
        $name=CONTROLLER_NAME;
        //$model = D ($name);
        $pk = $this->m->getPk ();
        $id = $_GET [$pk];
        $condition = array ($pk => array ('in', $id ) );
        if (false !== $this->m->resume ( $condition )) {
            $this->assign ( "jumpUrl", $this->getReturnUrl () );
            $this->success ( '状态恢复成功！' );
        } else {
            $this->error ( '状态恢复失败！' );
        }
    }


    function saveSort() {
        $seqNoList = $_POST ['seqNoList'];
        if (! empty ( $seqNoList )) {
            //更新数据对象
            $name=CONTROLLER_NAME;
            //$model = D ($name);
            $col = explode ( ',', $seqNoList );
            //启动事务
            $this->m->startTrans ();
            foreach ( $col as $val ) {
                $val = explode ( ':', $val );
                $this->m->id = $val [0];
                $this->m->sort = $val [1];
                $result = $this->m->save ();
                if (! $result) {
                    break;
                }
            }
            //提交事务
            $this->m->commit ();
            if ($result!==false) {
                //采用普通方式跳转刷新页面
                $this->success ( '更新成功' );
            } else {
                $this->error ( $this->m->getError () );
            }
        }
    }

    protected function msgText($nextModel,$nextModelText,$id){
        $app = __APP__;
        $url = __CONTROLLER__;

        return "发布成功!  <a href='$app/$nextModel/add'>发布{$nextModelText}信息</a> <a href='$url/edit/id/$id'>返回修改信息</a> <a href='$url/'>返回列表</a>";
    }

    public function show($content="",$charset='',$contentType='',$prefix=''){
        $id = I('id');
        $vo = $this->m->getById ( $id );
        if (method_exists ( $this, '_show' )) {
            $this->_show ( $vo );
        }
        $this->vo = $vo;
        $this->toview();

    }



    //==================自己加的==================//


    //保存添加和编辑
    function save() {
        //var_dump($this->isAjax());exit;
        //$id = I($this->m->getPk ());
        $id = I('id');
        //$vo = $this->m->getById ( $id );


        //自动验证
        $tableInfo = new TableInfo();
        $rules = $tableInfo->getValidateRules($this->m->getTableName());

        if(empty($id)){
            unset($_POST['id']);
            $_POST['uid'] = $this->uid; //添加时默认加上用户id
            if (false === $this->m->validate($rules)->create ()) {
                $this->error ( $this->m->getError () );
            }
            $r=$this->m->add ();
        }else{
            if (false === $this->m->create ()) {
                $this->error ( $this->m->getError () );
            }
            $r=$this->m->save ();
        }
        //保存当前数据对象

        //echo $this->m->getLastSql();exit;
        if ($r!==false) { //保存成功
            //$this->assign ( 'jumpUrl', cookie( '_currentUrl_' ) );
            $this->success ('保存成功!',cookie( '_currentUrl_' ));
        } else {
            //失败提示
            $this->error ('保存失败!');
        }


    }

    function responseFormat(){
        $format = "";
        if(IS_AJAX || C('RETRUN_FORMAT') == "android_json" || I('ret_format') == 'json' || $_SERVER['HTTP_ACCEPT'] == 'application/json'){ //json,app: code,msg,data
            return "json";
        }elseif (!empty(I(C('VAR_JSONP_HANDLER')))){ //jsonp
            return "jsonp";
        }elseif(isMobile()){
            return "wap";
        }else{
            return "web";
        }
    }

    /**
     * @name 根据请求方式，显示对应的格式到页面
     * @param  数据  array $data
     * @param  格式类型  int $type
     * @return   member
     */
    public function toview($data = "",  $tpl=""){
        if(empty($data)) $data = $this->get();
        //if(!empty($tpl)) $this->display($tpl);
        //var_dump($_SERVER);exit;
        if(IS_AJAX || C('RETRUN_FORMAT') == "android_json" || I('ret_format') == 'json' || $_SERVER['HTTP_ACCEPT'] == 'application/json'){ //json,app: code,msg,data
            if(empty($data)) $data = (object)$data;
            $this->success($data,"",1);
        }elseif (!empty(I(C('VAR_JSONP_HANDLER')))){ //jsonp
            $this->ajaxReturn(array("code" =>1, "msg" => "","data" => $data),'JSONP');
        }elseif(isMobile()){ //wap
            empty($tpl) && $tpl = ACTION_NAME;
            $wapTpl = "wap_".$tpl;
            $templateFile   =   $this->view->parseTemplate($wapTpl);

            //var_dump($templateFile);exit;
            //if()
            if("http://".$_SERVER['HTTP_HOST'] !=URL_M && "http://".$_SERVER['HTTP_HOST'] != URL_USER) redirect(URL_M.__SELF__);
            if(is_file($templateFile)) $this->display($wapTpl);
            else $this->display($tpl);
        }else{ //web
            $this->display($tpl);
        }

    }

    function success($message='成功',$jumpUrl='',$ajax=false){
        $this->dispatchJump2($message,1,$jumpUrl,$ajax);
    }
    function error($message='',$jumpUrl='',$ajax=false){
        $status = 0;
        if("json" == $this->responseFormat()) $ajax = 1;
        if($ajax || IS_AJAX) {// AJAX提交
            $data           =   is_array($ajax)?$ajax:array();
            $data['code'] =   $status;
            $data['msg']   =   $message;
            $data['data']    =   (object)array();
            $this->ajaxReturn($data);
        }
        if(is_int($ajax)) $this->assign('waitSecond',$ajax);
        if(!empty($jumpUrl)) $this->assign('jumpUrl',$jumpUrl);
        // 提示标题
        $this->assign('msgTitle',$status? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if($this->get('closeWin'))    $this->assign('jumpUrl','javascript:window.close();');
        $this->assign('status',$status);   // 状态
        //保证输出不受静态缓存影响
        C('HTML_CACHE_ON',false);
        if($status) { //发送成功信息
            $this->assign('message',$message);// 提示信息
            // 成功操作后默认停留1秒
            if(!isset($this->waitSecond))    $this->assign('waitSecond','1');
            // 默认操作成功自动返回操作前页面
            if(!isset($this->jumpUrl)) $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
            $this->display(C('TMPL_ACTION_SUCCESS'));
        }else{
            $this->assign('error',$message);// 提示信息
            //发生错误时候默认停留3秒
            if(!isset($this->waitSecond))    $this->assign('waitSecond','3');
            // 默认发生错误的话自动返回上页
            if(!isset($this->jumpUrl)) $this->assign('jumpUrl',"javascript:history.back(-1);");
            $this->display(C('TMPL_ACTION_ERROR'));
            // 中止执行  避免出错后继续执行
            exit ;
        }
    }

    function dispatchJump2($message='',$status = 1,$jumpUrl='',$ajax=false){
        if($ajax || IS_AJAX) {// AJAX提交
            $data           =   is_array($ajax)?$ajax:array();
            $data['code'] =   $status;
            $data['msg']    =   "";
            $data['data']   =   $message;

            $this->ajaxReturn($data);
        }
        if(is_int($ajax)) $this->assign('waitSecond',$ajax);
        if(!empty($jumpUrl)) $this->assign('jumpUrl',$jumpUrl);
        // 提示标题
        $this->assign('msgTitle',$status? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
        //如果设置了关闭窗口，则提示完毕后自动关闭窗口
        if($this->get('closeWin'))    $this->assign('jumpUrl','javascript:window.close();');
        $this->assign('status',$status);   // 状态
        //保证输出不受静态缓存影响
        C('HTML_CACHE_ON',false);
        if($status) { //发送成功信息
            $this->assign('message',$message);// 提示信息
            // 成功操作后默认停留1秒
            if(!isset($this->waitSecond))    $this->assign('waitSecond','1');
            // 默认操作成功自动返回操作前页面
            if(!isset($this->jumpUrl)) $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
            $this->display(C('TMPL_ACTION_SUCCESS'));
        }else{
            $this->assign('error',$message);// 提示信息
            //发生错误时候默认停留3秒
            if(!isset($this->waitSecond))    $this->assign('waitSecond','3');
            // 默认发生错误的话自动返回上页
            if(!isset($this->jumpUrl)) $this->assign('jumpUrl',"javascript:history.back(-1);");
            $this->display(C('TMPL_ACTION_ERROR'));
            // 中止执行  避免出错后继续执行
            exit ;
        }
    }

    //用户信息
    function userinfo(){
        if(empty($this->uid)) return;

        $u = M('User');
        $userinfo = $u->find($this->uid);
        unset($userinfo['id']);
        unset($userinfo['pwd']);
        unset($userinfo['open_id']);
        unset($userinfo['bind']);
        $userinfo = json_encode($userinfo);
        $this->userinfo = $userinfo;

    }

    //右边栏
    function right(){
        $f = M('Family');
        $r = $f->where("status = 1")->order("num desc")->limit(5)->select();
        $this->listByNum = $r;

    }

    //设置标题
    function setTitle($title){
        $this->pageTitle = empty($title) ? C('SITE_TITLE') :  $title.'_'.C('SITE_TITLE');
        //$title && $title = $title."_";
        //$this->pageTitle = $title.C('SITE_TITLE');
    }

    //验证码
    public function createVerifyCode(){
        $Verify = new \Think\Verify();
        $Verify->entry();
    }

}
