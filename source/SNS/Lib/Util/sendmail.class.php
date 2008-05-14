<?php
function sendemail ( $sendto_email,$subject, $body, $extra_hdrs) {
	require("./Include/phpmailer/class.phpmailer.php");

	$mail = new PHPMailer();
	$mail->IsSMTP();                // send via SMTP
	$mail->Host = C('SMTP_HOST');		// SMTP servers
	$mail->SMTPAuth = true;         // turn on SMTP authentication
	$mail->Username = C('SMTP_USER');		// SMTP username
	$mail->Password = C('SMTP_PASSWORD');        // SMTP password

	$mail->From = C('SMTP_SNEDER_EMAIL');      // 发件人邮箱
	$mail->FromName = C('SMTP_SENDER');  // 发件人

	$mail->CharSet = "UTF-8";            // 这里指定字符集！
	$mail->Encoding = "base64";
	if(is_array($sendto_email)){
		foreach($sendto_email as $v){
			$mail->AddAddress($v);
		}
	}else{
		$mail->AddAddress($sendto_email);  // 收件人邮箱和姓名
	}
	$mail->AddReplyTo(C('SMTP_SENDER_EMAIL'),C('SMTP_SENDER'));
	//$mail->WordWrap = 50; // set word wrap
	//$mail->AddAttachment("/var/tmp/file.tar.gz"); // attachment
	//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");
	$mail->IsHTML(true);  // send as HTML
	// 邮件主题
	$mail->Subject = $subject;
	// 邮件内容
	$mail->Body = $body;
	$mail->AltBody ="text/html";
	if(!$mail->Send()){
		return false;
	}else {
		return true;
	}
}
?>