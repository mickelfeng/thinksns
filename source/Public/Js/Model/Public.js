//Author: Fantasy
//create-time:08/05/06
//last-modify-time:08/05/07

/**
 * 正则验证Email
 *
 */
function isEmail(strValue)
{
var regTextEmail = /^[\w-.]+@[\w-]+(\.(\w)+)*(\.(\w){2,3})$/;
return regTextEmail.test(strValue);
}

var emailflag = 0;
var nameflag = 0;
var passflag = 0;
var repassflag = 0;
var agreeflag = 0;

/**
 * 密码强度
 *
 */
//CharMode函数
//测试某个字符是属于哪一类
function CharMode(iN) {
   if (iN>=48 && iN <=57) //数字
    return 1;
   if (iN>=65 && iN <=90) //大写字母
    return 2;
   if (iN>=97 && iN <=122) //小写
    return 4;
   else
    return 8; //特殊字符
}

//bitTotal函数
//计算出当前密码当中一共有多少种模式
function bitTotal(num) {
   modes=0;
   for (i=0;i<4;i++) {
    if (num & 1) modes++;
     num>>>=1;
    }
   return modes;
}

//checkStrong函数
//返回密码的强度级别
function checkStrong(sPW) {
   if (sPW.length<=4)
    return 0; //密码太短
    Modes=0;
    for (i=0;i<sPW.length;i++) {
     //测试每一个字符的类别并统计一共有多少种模式
     Modes|=CharMode(sPW.charCodeAt(i));
   }
   return bitTotal(Modes);
}

//pwStrength函数
//当用户放开键盘或密码输入框失去焦点时,根据不同的级别显示不同的颜色

function pwStrength(pwd) {
   O_color="#eeeeee";
   L_color="#FF0000";
   M_color="#FF9900";
   H_color="#33CC00";
   if (pwd==null||pwd==''){
    Lcolor=Mcolor=Hcolor=O_color;
   }
   else {
    S_level=checkStrong(pwd);
    switch(S_level) {
    case 0:
     Lcolor=Mcolor=Hcolor=O_color;
    case 1:
     Lcolor=L_color;
     Mcolor=Hcolor=O_color;
    break;
    case 2:
     Lcolor=Mcolor=M_color;
     Hcolor=O_color;
    break;
    default:
     Lcolor=Mcolor=Hcolor=H_color;
    }
   }
   document.getElementById("strength_L").style.background=Lcolor;
   document.getElementById("strength_M").style.background=Mcolor;
   document.getElementById("strength_H").style.background=Hcolor;
return;
}

/**
 * 注册验证
 *
 */
$(function(){

	$("#email").blur(function(){
		if($(this).val()==''){
			$("#emailwarn").html('<img src="'+PUBLIC+'/Images/check_error.gif" />不能为空');
			emailflag = 0; 
		}else if(!isEmail($(this).val())){
			$("#emailwarn").html('<img src="'+PUBLIC+'/Images/check_error.gif" />格式不对');
			emailflag = 0; 
		}else{
			$.post(APP+'/Public/checkemail',{email: $(this).val()},function(txt){
				if(txt){
					$("#emailwarn").html('<img src="'+PUBLIC+'/Images/check_right.gif" />');
					emailflag = 1; 
				}else{
					$("#emailwarn").html('<img src="'+PUBLIC+'/Images/check_error.gif" />已被注册');
					emailflag = 0; 
				}
			});
		}
	});
	
	$("#name").blur(function(){
		if (JHshStrLen($(this).val()) < 4) {
			$("#userwarn").html('<img src="'+PUBLIC+'/Images/check_error.gif" />名字太短');
			nameflag = 0;
		}else {
			$("#userwarn").html('<img src="'+PUBLIC+'/Images/check_right.gif" />');
			nameflag = 1; 
		}
	});

	$("#password").blur(function(){
		if($(this).val().length<6){
			$("#passwarn").html('<img src="'+PUBLIC+'/Images/check_error.gif" />不能小于6位');
			passflag = 0;
		}else{
			$("#passwarn").html('<img src="'+PUBLIC+'/Images/check_right.gif" />');
			passflag = 1; 
		}
	});

	$("#repassword").blur(function(){
		if($(this).val()!=$("#password").val()){
			$("#repswarn").html('<img src="'+PUBLIC+'/Images/check_error.gif" /> 两次不一致');
			repassflag = 0; 
		}else{
			$("#repswarn").html('<img src="'+PUBLIC+'/Images/check_right.gif" />');
			repassflag = 1; 
		}
	});
	
	$("#agreement").click(function(){
		if (!agreeflag) {
			agreeflag = 1;
		}else{
			agreeflag = 0;
		}
	});
	
	$("#sub").click(function(){
		if(emailflag && nameflag && passflag && repassflag && agreeflag){
			return true;
		}else{			
			alert("请正确填写信息!");
			return false;
		}
	});
	
});
