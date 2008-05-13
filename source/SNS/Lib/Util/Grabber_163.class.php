<?php
//set_time_limit(100);
//error_reporting(E_ALL);

//$c	=	get163Contacts($email, $passwd, $Err);
//if (!$c === FALSE){
//	print_r($c);
//}else{
//	print_r($Err);
//}
class Grabber_163 {
	function get($Username, $Password, &$Err){
			$sid = array();

			$ch = curl_init();

			curl_setopt ($ch, CURLOPT_POST, TRUE);
			curl_setopt ($ch, CURLOPT_HEADER, FALSE);
			curl_setopt ($ch, CURLOPT_USERAGENT, "ASO");
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt ($ch, CURLOPT_REFERER, "");
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt ($ch, CURLOPT_MAXREDIRS, 5);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt ($ch, CURLOPT_COOKIESESSION, TRUE); # PHP5 only
			curl_setopt ($ch, CURLOPT_COOKIEFILE, "cookie.txt");
			curl_setopt ($ch, CURLOPT_COOKIEJAR,  "cookie.txt");
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

			curl_setopt ($ch, CURLOPT_URL, "http://reg.163.com/login.jsp?type=1&url=http://fm163.163.com/coremail/fcg/ntesdoor2?lightweight%3D1%26verifycookie%3D1%26language%3D-1%26style%3D16");

			$Data = "";
			$Data .= "verifycookie=0&";
			$Data .= "username=".urlencode($Username)."&";
			$Data .= "password=".urlencode($Password)."&";
			// $Data .= "PersistentCookie=Yes&";
			$Data .= "selType=jy&";
			$Data .= "product=mail163&";
			$Data .= "style=16";

			curl_setopt ($ch, CURLOPT_POSTFIELDS, $Data);

			$contents = curl_exec($ch);
			$ErrNo = curl_errno($ch);

			if ($ErrNo != 0)
			{
				$Err[] = "cURL Error @ Login :: Error No: ".curl_errno($ch).' : '.curl_error($ch);
				return FALSE;
			}

			if ($contents == NULL)
			{
				$Err[] = "contents == NULL @ Login";
				return FALSE;
			}

			curl_setopt ($ch, CURLOPT_POST, FALSE);
			$url = "http://fm163.163.com/coremail/fcg/ntesdoor2?lightweight=1&verifycookie=1&language=-1&style=16&username=".urlencode($Username);
			curl_setopt ($ch, CURLOPT_URL, $url);

			$contents = curl_exec($ch);
			$ErrNo = curl_errno($ch);

			if ($ErrNo != 0)
			{
				$Err[] = "cURL Error @ Contacts :: Error No: ".curl_errno($ch).' : '.curl_error($ch);
				return FALSE;
			}

			if ($contents == NULL)
			{
				$Err[] = "contents == NULL @ Contacts";
				return FALSE;
			}
			if( preg_match("/sid=([a-z0-9A-Z]*)\"/i", $contents, $sid) === false )
			{
				$Err[] = "Can't get the session id passed by $_GET.";
				return FALSE;
			}

			curl_setopt ($ch, CURLOPT_POST, FALSE);
			$url = "http://g1a3.mail.163.com/coremail/fcg/ldvcapp?funcid=prtsearchres&sid=".$sid[1]."&listnum=0&showlist=all&tempname=address%2faddress.htm";
			curl_setopt ($ch, CURLOPT_URL, $url);

			$contents = curl_exec($ch);
			$ErrNo = curl_errno($ch);

			if ($ErrNo != 0)
			{
				$Err[] = "cURL Error @ Contacts :: Error No: ".curl_errno($ch).' : '.curl_error($ch);
				return FALSE;
			}

			if ($contents == NULL)
			{
				$Err[] = "contents == NULL @ Contacts";
				return FALSE;
			}

			//return $contents;
			preg_match_all("/�޸� -->(.+)/",$contents,$result1);
			preg_match_all("/���� -->(.+)<\/td>/",$contents,$result2);

			$count	=	count($result1[1]);
			for($i=0;$i<$count;$i++){
				$email[$i][0]	=	$result1[1][$i];
				$email[$i][1]		=	$result2[1][$i];
			}
			return $email;
	}
}
?>