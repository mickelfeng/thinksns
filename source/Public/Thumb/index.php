<?php
//�Զ�����ͼ ���� url|w|h|type="cut/full"|mark="text/image|r"
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
//��ʱĿ¼
$tempDir	=	"./temp/";
//��ͼĿ¼
$thumbDir	=	"./thumb/";
$tempFile	=	$tempDir.$fileHash;
$thumbFile	=	$thumbDir.$fileHash."_".$w."_".$h."_".$t;
//�ж��Ƿ��滻
if(!$r){
	//�ж��Ƿ����
	if(file_exists($thumbFile)){
		$img->showImg($thumbFile);
		exit;
	}
}
//���������
if(copy($url,$tempFile)){
	//�ж�ͼƬ��С ���ͼƬ��͸߶�С��Ҫ���ŵı��� ֱ�����
	$info	=	$img->getImageInfo($tempFile);
	if($info['width']<=$w && $info['height']<=$h){
		copy($tempFile,$thumbFile);
		$img->showImg($thumbFile,'',$info['width'],$info['height']);
		exit;
	}else{
		//������ͼ
		if($t=='c'){
			$thumb	=	$img->cutThumb($tempFile,$thumbFile,$w,$h);
		}elseif($t=='f'){
			$thumb	=	$img->thumb($tempFile,'',$thumbFile,$w,$h);
		}
		//�����ͼ
		$img->showImg($thumb,'',$w,$h);
		exit;
	}
}
?>