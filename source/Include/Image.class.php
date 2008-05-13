<?php
class Image
{//类定义开始

    /**
     +----------------------------------------------------------
     * 架构函数
     *
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    function __construct()
    {

    }

    /**
     +----------------------------------------------------------
     * 取得图像信息
     *
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $image 图像文件名
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if( $imageInfo!== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]),1));
            $imageSize = filesize($img);
            $info = array(
                "width"=>$imageInfo[0],
                "height"=>$imageInfo[1],
                "type"=>$imageType,
                "size"=>$imageSize,
                "mime"=>$imageInfo['mime']
            );
            return $info;
        }else {
            return false;
        }
    }

    /**
     +----------------------------------------------------------
     * 显示服务器图像文件
     * 支持URL方式
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $imgFile 图像文件名
     * @param string $text 文字字符串
     * @param string $width 图像宽度
     * @param string $height 图像高度
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    function showImg($imgFile,$text='',$width=80,$height=30) {
        //获取图像文件信息
		//2007/6/26 增加图片水印输出，$text为图片的完整路径即可
		$info = Image::getImageInfo($imgFile);
        if($info !== false) {
            $createFun  =   str_replace('/','createfrom',$info['mime']);
            $im = $createFun($imgFile);
            if($im) {
                $ImageFun= str_replace('/','',$info['mime']);
				//水印开始
                if(!empty($text)) {
                    $tc  = imagecolorallocate($im, 0, 0, 0);
					if(is_file($text)&&file_exists($text)){
						// 取得水印信息
						$textInfo = Image::getImageInfo($text);
						$createFun2= str_replace('/','createfrom',$textInfo['mime']);
						$waterMark = $createFun2($text);
						$imgW	=	$info["width"];
						$imgH	=	$info["width"]*$textInfo["height"]/$textInfo["width"];
						$y	=	($info["height"]-$textInfo["height"])/2;
						if(function_exists("ImageCopyResampled"))
							ImageCopyResampled($im,$waterMark,0,$y,0,0, $imgW,$imgH, $textInfo["width"],$textInfo["height"]);
						else
							ImageCopyResized($im,$waterMark,0,$y,0,0,$imgW,$imgH,  $textInfo["width"],$textInfo["height"]);
					}else{
						imagestring($im, 3, 5, 5, $text, $tc);
					}
					//ImageDestroy($tc);
                }
				//水印结束
                if($info['type']=='png' || $info['type']=='gif') {
                imagealphablending($im, FALSE);//取消默认的混色模式
                imagesavealpha($im,TRUE);//设定保存完整的 alpha 通道信息
                }
                Header("Content-type: ".$info['mime']);
                $ImageFun($im);
                @ImageDestroy($im);
                return ;
            }
        }
        //获取或者创建图像文件失败则生成空白PNG图片
        $im  = imagecreatetruecolor($width, $height);
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        imagestring($im, 4, 5, 5, "no pic", $tc);
        Image::output($im);
        return ;
    }

	// 切割缩图cutThumb
	// 2007/6/15
	function cutThumb($image,$filename='',$maxWidth='200',$maxHeight='50',$warterMark='',$type='',$interlace=true,$suffix='_thumb')
	{
        // 获取原图信息
        $info  = Image::getImageInfo($image);
         if($info !== false) {
            $srcWidth  = $info['width'];
            $srcHeight = $info['height'];
            $pathinfo = pathinfo($image);
			$type =  $pathinfo['extension'];
            $type = empty($type)?$info['type']:$type;
			$type	=	strtolower($type);
            $interlace  =  $interlace? 1:0;
            unset($info);
            // 载入原图
            $createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
            $srcImg     = $createFun($image);

            //创建缩略图
            if($type!='gif' && function_exists('imagecreatetruecolor'))
                $thumbImg = imagecreatetruecolor($maxWidth, $maxHeight);
            else
                $thumbImg = imagecreate($maxWidth, $maxHeight);
			// 计算缩放比例
			if(($maxWidth/$maxHeight)>=($srcWidth/$srcHeight)){
				//宽不变,截高，从中间截取 y=
				$width	=	$srcWidth;
				$height	=	$srcWidth*($maxHeight/$maxWidth);
				$x		=	0;
				$y		=	($srcHeight-$height)*0.5;
			}else{
				//高不变,截宽，从中间截取，x=
				$width	=	$srcHeight*($maxWidth/$maxHeight);
				$height	=	$srcHeight;
				$x		=	($srcWidth-$width)*0.5;
				$y		=	0;
			}
			// 复制图片
			if(function_exists("ImageCopyResampled")){
				ImageCopyResampled($thumbImg, $srcImg, 0, 0, $x, $y, $maxWidth, $maxHeight, $width,$height);
			}else{
				ImageCopyResized($thumbImg, $srcImg, 0, 0, $x, $y, $maxWidth, $maxHeight,  $width,$height);
			}
			ImageDestroy($srcImg);
			/*水印开始* /
			if($warterMark){
				//计算水印的位置,默认居中
				$textInfo = Image::getImageInfo($warterMark);
				$textW	=	$textInfo["width"];
				$textH	=	$textInfo["height"];
				unset($textInfo);
				$mark = imagecreatefrompng($warterMark);
				$imgW	=	$width;
				$imgH	=	$width*$textH/$textW;
				$y		=	($height-$textH)/2;
				if(function_exists("ImageCopyResampled")){
					ImageCopyResampled($thumbImg,$mark,0,$y,0,0, $imgW,$imgH, $textW,$textH);
				}else{
					ImageCopyResized($thumbImg,$mark,0,$y,0,0,$imgW,$imgH,  $textW,$textH);
				}
				ImageDestroy($mark);
			}
			/*水印结束*/
            if('gif'==$type || 'png'==$type) {
				//imagealphablending($thumbImg, FALSE);//取消默认的混色模式
                //imagesavealpha($thumbImg,TRUE);//设定保存完整的 alpha 通道信息
                $background_color  =  ImageColorAllocate($thumbImg,  0,255,0);
				//  指派一个绿色
				imagecolortransparent($thumbImg,$background_color);
				//  设置为透明色，若注释掉该行则输出绿色的图
            }

            // 对jpeg图形设置隔行扫描
            if('jpg'==$type || 'jpeg'==$type) 	imageinterlace($thumbImg,$interlace);

            // 生成图片
            $imageFun = 'image'.($type=='jpg'?'jpeg':$type);
            $filename  = empty($filename)? substr($image,0,strrpos($image, '.')).$suffix.'.'.$type : $filename;

            $imageFun($thumbImg,$filename);
            ImageDestroy($thumbImg);
            return $filename;
         }
         return false;

	}
    /**
     +----------------------------------------------------------
     * 生成缩略图
     *
     +----------------------------------------------------------
     * @static
     * @access public
     +----------------------------------------------------------
     * @param string $image  原图
     * @param string $type 图像格式
     * @param string $filename 缩略图文件名
     * @param string $maxWidth  宽度
     * @param string $maxHeight  高度
     * @param string $position 缩略图保存目录
     * @param boolean $interlace 启用隔行扫描
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
	 //2007/7/18 添加水印缩略图
    function thumb($image,$type='',$filename='',$maxWidth=200,$maxHeight=50,$warterMark='',$interlace=true,$suffix='_thumb')
    {
        // 获取原图信息
        $info  = Image::getImageInfo($image);
         if($info !== false) {
            $srcWidth  = $info['width'];
            $srcHeight = $info['height'];
            $pathinfo = pathinfo($image);
            $type =  $pathinfo['extension'];
            $type = empty($type)?$info['type']:$type;
			$type	=	strtolower($type);
			$interlace  =  $interlace? 1:0;
            unset($info);
            $scale = min($maxWidth/$srcWidth, $maxHeight/$srcHeight); // 计算缩放比例
            // 缩略图尺寸
            $width  = (int)($srcWidth*$scale);
            $height = (int)($srcHeight*$scale);
            // 载入原图
            $createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
            $srcImg     = $createFun($image);
            //创建缩略图
            if($type!='gif' && function_exists('imagecreatetruecolor'))
                $thumbImg = imagecreatetruecolor($width, $height);
            else
                $thumbImg = imagecreate($width, $height);
            // 复制图片
            if(function_exists("ImageCopyResampled"))
                ImageCopyResampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth,$srcHeight);
            else
                ImageCopyResized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height,  $srcWidth,$srcHeight);
            ImageDestroy($srcImg);
			/*
			//水印开始
				//计算水印的位置,默认居中
				$textInfo = Image::getImageInfo($warterMark);
				$textW	=	$textInfo["width"];
				$textH	=	$textInfo["height"];
				unset($textInfo);
				$mark = imagecreatefrompng($warterMark);
				$imgW	=	$width;
				$imgH	=	$width*$textH/$textW;
				$y		=	($height-$textH)/2;
				if(function_exists("ImageCopyResampled")){
					ImageCopyResampled($thumbImg,$mark,0,$y,0,0, $imgW,$imgH, $textW,$textH);
				}else{
					ImageCopyResized($thumbImg,$mark,0,$y,0,0,$imgW,$imgH,  $textW,$textH);
				}
				ImageDestroy($mark);
			//水印结束
			*/
            if('gif'==$type || 'png'==$type) {
				imagealphablending($thumbImg, FALSE);//取消默认的混色模式
                imagesavealpha($thumbImg,TRUE);//设定保存完整的 alpha 通道信息
                $background_color  =  ImageColorAllocate($thumbImg,  0,255,0);//  指派一个绿色
				imagecolortransparent($thumbImg,$background_color);//  设置为透明色，若注释掉该行则输出绿色的图
            }
            if('jpg'==$type || 'jpeg'==$type) {
				imageinterlace($thumbImg,$interlace);// 对jpeg图形设置隔行扫描
			}
            // 生成图片
            $imageFun = 'image'.($type=='jpg'?'jpeg':$type);
            $filename  = empty($filename)? substr($image,0,strrpos($image, '.')).$suffix.'.'.$type : $filename;
            $imageFun($thumbImg,$filename);
			ImageDestroy($thumbImg);
            return $filename;
         }
         return false;
    }

    function output($im,$type='png')
    {
        Header("Content-type: image/".$type);
        $ImageFun='Image'.$type;
        $ImageFun($im);
        ImageDestroy($im);
    }


}//类定义结束
?>