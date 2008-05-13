<?php
//自动缩略图 参数 url|w|h|type="cut/full"|mark="text/image|r"
include_once("../../Include/Image.class.php");
$url	=	$_GET['url'];
$url	=	str_replace("./Public/","../",$url);
if(empty($url)){
	$url	=	"../Images/noface.gif";
}
$w = $_GET['w']?$_GET['w']:'100';
$h = $_GET['h']?$_GET['h']:'100';
$t = $_GET['t']?$_GET['t']:'c';
$r = $_GET['r']?'1':'0';
$img	=	new Image();
$fileHash	=	md5($url.$w.$h);
//临时目录
$tempDir	=	"./temp/";
//缩图目录
$thumbDir	=	"./thumb/";
$tempFile	=	$tempDir.$fileHash;
$thumbFile	=	$thumbDir.$fileHash."_".$w."_".$h."_".$t;
//判断是否替换
if(!$r){
	//判断是否存在
	if(file_exists($thumbFile)){
		$img->showImg($thumbFile);
		exit;
	}
}
//不存在输出
if(copy($url,$tempFile)){
	//判断图片大小 如果图片宽和高都小于要缩放的比例 直接输出
	$info	=	$img->getImageInfo($tempFile);
	if($info['width']<=$w && $info['height']<=$h){
		copy($tempFile,$thumbFile);
		$img->showImg($thumbFile,'',$info['width'],$info['height']);
		exit;
	}else{
		//生成缩图
		if($t=='c'){
			$thumb	=	$img->cutThumb($tempFile,$thumbFile,$w,$h);
		}elseif($t=='f'){
			$thumb	=	$img->thumb($tempFile,'',$thumbFile,$w,$h);
		}
		//输出缩图
		$img->showImg($thumb,'',$w,$h);
		exit;
	}
}
?>