<?php
class indexAction extends Action {

	public function index(){
		if(Cookie::get("password")){
			$this->redirect('','home');
		}
		$this->display();
	}
	//我新加上的
	function delAlert() {
		$id	=	intval($_GET[id]);
		$dao = D('Notify');

		$map = array('id'=>$id,'status'=>1);

		if($dao->save($map)){
			//$this->success("del success!",1);
			echo true;
		}else{
			//$this->error("删除失败！");
			echo false;
		}
	}

	function hello() {
		$this->assign("toUserId",$_GET[to]);
		$this->display();
	}
	function sayHello() {
		$dao = D("Hello");
		$dao->fromUserId = Session::get(C('USER_AUTH_KEY'));
		$dao->toUserId = $_POST[to];
		$dao->cTime = time();
		if($result = $dao->add()){
			$this->addUserAlert($_POST[to],"sayHello");
			echo "<script>javascript:window.close();</script>";
		}else{
			$this->error('打招呼失败！');
		}
	}
	function re() {
		$id	=	intval($_GET[id]);
		$dao = D('Notify');
		$list = $dao->getById($id);
		if($list){
			//修改状态
			$map = array('id'=>$id,'status'=>1);
			$dao->save($map);
			switch($list->action){
				case "sayHello":
					$url	= __APP__."/Friend/hello/to/".$list->fromUserId;
					break;
				case "addFriend":
					$url	= __APP__."/Friend/mkfriend/id/".$list->fromUserId;
					break;
				case "sendMessage":
					$url	= __APP__."/Message/";
					break;
			}
			redirect($url);
		}else{
			$this->error("错误的通知回复！");
		}
	}
}
?>