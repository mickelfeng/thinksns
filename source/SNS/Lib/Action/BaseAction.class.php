<?php
import("ORG.Text.Validation");
import("ORG.Util.HashMap");
include_cache("./Include/Page.class.php");
class BaseAction extends Action
{
	var $mid;	//当前登陆的用户ID = mid
	var $uid;	//当前浏览的用户ID = uid
	protected  function _initialize(){
		//判断用户登录
		$this->mid	=	Session::get("mid");
		$this->uid	=	$_REQUEST['uid'];

		if(empty($this->mid)){
			//登陆验证
			$this->redirect("checklogin","Public");
			exit;
		}
		if(empty($this->uid)){
			$this->uid	=	$this->mid;
		}

		//未读短信条数
		$msgCount	=	D("Message")->count("toUserId=".$this->mid." and status=0");
		//在线状态
		//$online	=	$this->checkOnline();

		$networkId	=	D('User')->find($this->mid,'networkId')->networkId;
		if(empty($networkId)) $networkId=1;
		$network	=	D('Network')->find($networkId);
		//注册模板变量
		$this->assign("msgCount",$msgCount);
		$this->assign('network',$network);
		$this->assign('mid',$this->mid);
		$this->assign('uid',$this->uid);

		parent::_initialize();
    }

	//function _empty() {
		//$this->redirect('index',"index");
	//}

	// 发送通知
	protected function addUserAlert($toUserId,$action='',$info='') {
		//action = makeFriend sayHello birthday  system
		$fromUserId=Session::get(C('USER_AUTH_KEY'));
		$dao = D("Notify");
		$dao->fromUserId = $fromUserId;
		$dao->toUserId = $toUserId;
		$dao->action = $action;
		$dao->info	 = $info;
		$dao->cTime = time();
		$result = $dao->add();
		return $result;
	}
	//记录用户动态，增加info字段来缓存动态内容，减少服务器查询压力
	protected function addUserFeed($userId='',$do,$action,$recordId,$title,$info) {
		if($userId!=''){
			$dao = D("UserFeed");
			$dao->userId = $userId;
			$dao->action = $action;
			$dao->recordId = $recordId;
			$dao->title =$title;
			$dao->info =$info;
			$dao->cTime = NOW;
			$dao->day	=	date('Y-m-d',NOW);
			$dao->status = 0;
			$result = $dao->add();
			return $result;
		}else{
			return false;
		}
	}
	//记录在线状态
    protected function checkOnline() {
        $dao = D("Online");
        // 记录最后访问时间
        $map	=	new HashMap();
        $ip		=	ip2long(get_client_ip());
        $map->put('activeTime',time());
		$map->put('userId',$this->mid);
        $map->put('uri',$_SERVER['PHP_SELF']);
        if($online  =  $dao->getBy('onlineIp',$ip)) {
            // 已经存在 更新最后访问时间
            $map->put('id',$online->id);
			$dao->save($map);
        }else {
            $map->put('onlineIp',$ip);
            $dao->add($map);
        }
		// 归档并删除24小时未在线用户
        $dao->deleteAll("(activeTime+86000)<".time());
        // 15分钟活动用户
        $count[]	=	$dao->count("(activeTime+900)>=".time());
		// 24小时活动用户
        $count[]	=	$dao->count("(activeTime+900)<".time());
        return $count;
    }
	//添加一个标签
	protected function addTag($tag='',$module=MODULE_NAME) {
		$tagId	=	D('Tag')->insert($tag,$module);
		if($tagId){
			return $tagId;
		}else{
			return '';
		}
	}
	protected function addTagIndex($tag='',$recordId='',$module=MODULE_NAME) {
		$tagId	=	$this->addTag($tag);
		if(empty($tagId)){
			$tags[$k]	=	'';
		}
		$indexDao	=	D('TagIndex');
		$index['tagId']		=	$tagId;
		$index['recordId']	=	$recordId;
		$index['module']	=	$module;
		$indexDao->create($index);
		$result = $indexDao->add();
		if($result){
			return $tagId;
		}else{
			return '';
		}
	}
	protected function addUserTag($tag='',$module='',$userId='') {
		$tagId	=	$this->addTag($tag);
		if(empty($tagId)){
			$tags[$k]	=	'';
		}
		if($userId==''){
			$userId	=	Session::get('mid');
		}
		$dao = D('UserTag');
		$map['userId']	=	$userId;
		$map['tagId']	=	$tagId;
		$map['module']	=	$module;
		$result = $dao->add();
		if($result){
			return $tagId;
		}else{
			return '';
		}
	}
	public function success($msg,$return=true) {
        if($ajax || $this->isAjax()) {
        	echo $return;
			exit;
        }else {
        	$this->assign('message',$msg);
            $this->forward();
        }
	}
	public function error($msg,$return=false) {
        if($ajax || $this->isAjax()) {
        	echo $return;
			exit;
        }else {
        	$this->assign('error',$msg);
            $this->forward();
        }
	}
	    /**
     +----------------------------------------------------------
     * Ajax方式返回数据到客户端
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 要返回的数据
     * @param String $type ajax返回类型 为空，则直接返回$data,为JSON,则以json形式返回
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
	public function myAjaxRetrun($data,$type) {
		header("Content-Type:text/html; charset=".C('OUTPUT_CHARSET'));
		if($type == ''){
			 echo $data;
		}elseif(strtoupper($type)=='JSON'){
			echo json_encode($data);
		}elseif(strtoupper($type)=='XML'){
			echo xml_encode($data);
		}

	}

	    /**
     +----------------------------------------------------------
     * Ajax方式返回数据到客户端
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $data 要返回的数据
     * @param String $info 提示信息
     * @param String $status 返回状态
     * @param String $type ajax返回类型 JSON XML
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function ajaxReturn($data='',$info='',$status='',$type='')
    {
		// 保存日志
		Log::save();
        $result  =  array();
        if($status === '') {
        	$status  = $this->get('error')?0:1;
        }
        if($info=='') {
            if($this->get('error')) {
                $info =   $this->get('error');
            }elseif($this->get('message')) {
                $info =   $this->get('message');
            }
        }
        $result['status']  =  $status;
   	    $result['info'] =  $info;
        $result['data'] = $data;
		if(empty($type)) $type	=	C('AJAX_RETURN_TYPE');
		if(strtoupper($type)=='JSON') {
			// 返回JSON数据格式到客户端 包含状态信息
			header("Content-Type:text/html; charset=".C('OUTPUT_CHARSET'));
			exit(json_encode($result));
		}elseif(strtoupper($type)=='XML'){
			// 返回xml格式数据
			header("Content-Type:text/xml; charset=".C('OUTPUT_CHARSET'));
			exit(xml_encode($result));
		}else{
			// TODO 增加其它格式
		}
    }

	protected function delete($module){
		$result  =  D($module)->delete('id='.$_POST['id'].' and userId='.$this->mid);
		if($result){
			$this->success("删除成功！");
		}else{
			$this->error("删除失败！");
		}
	}
	//执行单图上传操作
	protected function _upload($path) {
		if(!checkDir($path)){
			return '目录创建失败: '.$path;
		}
		import("ORG.Net.UploadFile");
        $upload = new UploadFile();

        //设置上传文件大小
        $upload->maxSize	=	'2000000' ;

        //设置上传文件类型
        $upload->allowExts	=	explode(',',strtolower('jpg,gif,png,jpeg'));

		//存储规则
		$upload->saveRule	=	'uniqid';
		//设置上传路径
		$upload->savePath	=	$path;
        //执行上传操作
        if(!$upload->upload()) {
            //捕获上传异常
            return $upload->getErrorMsg();
        }else{
			//上传成功
			return $upload->getUploadFileInfo();
    	}
    }
    function __destruct()
    {
    }
}//类定义结束
?>