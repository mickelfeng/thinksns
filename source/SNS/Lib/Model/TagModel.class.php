<?php
class TagModel extends Model
{
	//表单验证
	protected  $_validate = array(
		array('name','require','-1',1),
		array('module','require','-2',1),
	);
	//标签格式化,替换违规分割符,用空格或逗号分隔
	protected function clean($tags){
		if (!empty($tags)) {
			$tags = preg_replace('/\s+/','',$tags);
			$tags = preg_replace('/，+/',',',$tags);
			$tags = preg_replace('/,+/',',',$tags);
			$tags = preg_replace('/,$/','',$tags);
		}else{
			$tags='';
		}
		return $tags;
	}
	//标签解析
	public function parse($tags,$num=0){
		if (!empty($tags)) {
			$tags = $this->clean($tags);
			$tags = explode(',',$tags);
			//取前$num个
			if($num>0){
				$sum = count($tags);
				if(count($tags)>$num)
				$tags	=	array_slice($tags,0,$num);
			}
		}
		return $tags;
	}
	public function insert($tag,$module) {
		$map['name']	=	$tag;
		$map['module']	=	$module;
		if($tag	=	$this->find($map)){
			return $tag->id;
		}elseif($tagId	=	$this->add($map)){
			return $tagId;
		}else{
			return '';
		}
	}
}
?>