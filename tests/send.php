<?php

	require_once('Mail.php'); 
	require_once('Mail/mime.php'); 
	require_once('Net/SMTP.php'); 
	header("content-Type: text/html; charset=Utf-8"); 

	$domain = "bewk.top";								//网站地址
	
	switch ($domain) {
		case "bewk.top":
			$name = "贝壳加速器";
			$pswd = "sss";
            $surl = "https://".$domain;
            $burl = "https://www.vpsc.men";
            $mail = "bestvpn7@gmail.com";
            break;
		case "dewk.top":
			$name = "达克加速器";
			$pswd = "sss";
            $surl = "https://".$domain;
            $burl = "https://www.vpsa.men";
			$mail = "bestvpn7@gmail.com";
			break;
		case "kuee.top":
            $name = "快鱼加速器";
            $pswd = "sss";
            $surl = "https://".$domain;
            $burl = "https://www.auok.men";
			$mail = "kuaiyu168@gmail.com";
			break;
        case "xoke.top":
            $name = "小可加速器";
            $pswd = "sss";
            $surl = "https://".$domain;
            $burl = "https://www.sofu.men";
			$mail = "kuaiyu168@gmail.com";		
			break;	
		default:
            file_put_contents('log.txt',"$domain is error \n",FILE_APPEND);
        }

	$s = rand(30,99);
	sleep($s);

	$smtpinfo = array();     
	$smtpinfo["host"] = "ssl://smtp.zoho.com";					//SMTP服务器 
	$smtpinfo["port"] = "465"; 							        //SMTP服务器端口 
	$smtpinfo["username"] = "$mail"; 						    //发件人邮箱 
	$smtpinfo["password"] = "tenky_Admin8";						//发件人邮箱密码 
	$smtpinfo["timeout"] = 10;							        //网络超时时间，秒 
	$smtpinfo["auth"] = true;							        //登录验证

	$from = "$name <hi@$domain>";   						    //发件人显示信息 
	$contentType = "text/html; charset=utf-8"; 					//邮件正文类型，格式和编码
	$crlf = "\n"; 									            //换行符号 Linux: \n Windows: \r\n 

	$no = rand(1000,9999);
	$dlink = "<font color=Red size='4'><b>我们新发布 <a href='".$surl."/images/win/Windows.exe'>Windows</a>、<a href='".$surl."/images/mac/macOS.dmg'>macOS</a>、<a href='".$surl."/images/android/Android.apk'>Android</a> 官方客户端，请试一试！</b></font><br /><br />";
	$iphone = "<font color=DarkRed size='4'><b>iPhone/iPad</b>:请打开小火箭-->数据-->删除本地节点。然后参考教程重新订阅！</font><br /><br />";

	$subject = $name."，邮件通知：$no"; 	 				 
	$content = "<font size='4'><br />亲亲，系统检测您可能没连上节点！</font><br /> <br />
	<font color=Red size='4'>如账户过期，请登陆网站点击<b> 续费订阅</b> </font><br /> <br />
	<font color=Red size='4'>连不上的终极解决办法：删掉客户端，重启系统再下载。手机/电脑都试一试！</font><br /><br />$iphone
	<font color=Blue size='4'>通知编号：$no 是随机生成的数字，请忽略它</font> <br /> <br />
	<font color=Red size='4'>官方网址：<a href='".$surl."'>$surl</a> (<a href='".$burl."/index.php'>备用网址</a>)</font><br /> <br />
	<font color=Blue size='4'>这是系统邮件，请勿回复！发邮件至 <a href='mailto:".$mail."?Subject=最新网址'>$mail</a> 自动回复最新网址!</font> <br /> <br />";

	$param['text_charset'] = 'utf-8'; 
	$param['html_charset'] = 'utf-8'; 
	$param['head_charset'] = 'utf-8';

	$mime = new Mail_mime($crlf); 
	$mime->setHTMLBody($content);  
	$body = $mime->get($param); 

	$elist = explode("\n",file_get_contents('email.txt'));			    //所有邮件列表
  	
	if (count($elist)>1) {
	    $headers = array(); 
      	$headers["From"] = $from; 
	    $headers["To"] = $elist[0];     
	    $headers["Subject"] = $subject; 
	    $headers["Content-Type"] = $contentType; 
	    $headers = $mime->headers($headers); 
	        
	    $smtp =& Mail::factory("smtp",$smtpinfo); 
	    $mail = $smtp->send($elist[0],$headers,$body);
	    $smtp->disconnect(); 

       	if (PEAR::isError($mail)) {  
		$text =  $elist[0].' failed:'.$mail->getMessage()."\n"; 
		file_put_contents('log.txt',date("Y-m-d H:i:s")." ".$text,FILE_APPEND);
	    } else{ 
	        $text = $elist[0]." success!"."\n"; 
	        file_put_contents('log.txt',date("Y-m-d H:i:s")." ".$text,FILE_APPEND);
	        array_shift($elist);
        	file_put_contents('email.txt',implode("\n",$elist));
	    }
	} else {
	    echo file_put_contents('log.txt',"File email.txt is empty\n",FILE_APPEND);
	}
?>
