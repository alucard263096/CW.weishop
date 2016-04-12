<?php
		
			$settings=globaSetting();
			if (checksubmit("submit")) {
            $cfg = array(
				   		    'smtp_server' => $_GP['smtp_server'],
				   		    'smtp_port' => intval($_GP['smtp_port']),
				   		    'smtp_mail' => $_GP['smtp_mail'],
				   		    'smtp_username' => $_GP['smtp_username'],
				   		    'smtp_passwd' => $_GP['smtp_passwd'],
				   		    'smtp_title' => $_GP['smtp_title'],
				   		    'smtp_to_mail' => $_GP['smtp_to_mail'],
				   		    'smtp_authmode' => intval($_GP['smtp_authmode']),
				   		     'smtp_openmail' => intval($_GP['smtp_openmail']),
				   		  'mailtemplate' => htmlspecialchars_decode($_GP['mailtemplate'])
            );
      
    
                
          	refreshSetting($cfg);
            message('保存成功', 'refresh', 'success');
        }
        		if (checksubmit("sendmail")) {
            $settings = array(
				   		    'smtp_server' => $_GP['smtp_server'],
				   		    'smtp_port' => intval($_GP['smtp_port']),
				   		    'smtp_mail' => $_GP['smtp_mail'],
				   		    'smtp_username' => $_GP['smtp_username'],
				   		    'smtp_passwd' => $_GP['smtp_passwd'],
				   		    'smtp_title' => $_GP['smtp_title'],
				   		    'smtp_to_mail' => $_GP['smtp_to_mail'],
				   		    'smtp_authmode' => intval($_GP['smtp_authmode']),
				   		     'smtp_openmail' => intval($_GP['smtp_openmail']),
				   		  'mailtemplate' => htmlspecialchars_decode($_GP['mailtemplate'])
            );
               $good_line="";
            	
            			$good_line .= "<table>";
			$good_line .= "<tr><th style=\"text-align:left;\">名称</th><th  style=\"text-align:center;width:50px\">数量</th><th  style=\"text-align:center;width:50px\">单价</th>";
			$good_line .= "</tr>";
          
                    	
                    	
				$good_line .= "<tr><td  style=\"text-align:left\">[珍珠白+20]珍珠白BB霜晶彩焕颜修容霜50ml遮瑕强裸妆补水保湿持久防水";
			$good_line .= "</td><td  style=\"text-align:center\">2 </td>";
			$good_line .= "	<td  style=\"text-align:center\">200</td></tr>";
      
      				$good_line .= "<tr><td  style=\"text-align:left\">[珍珠白+10]珍珠白BB霜晶彩焕颜修容霜50ml遮瑕强裸妆补水保湿持久防水";
			$good_line .= "</td><td  style=\"text-align:center\">1 </td>";
			$good_line .= "	<td  style=\"text-align:center\">100 </td></tr>";
			              	
	$good_line .= "	</table>";
            
            
            
            $previewtmp=$settings['mailtemplate'];            
  for($i=1;$i<3;$i++)
	{
		 $previewtmp=str_replace("{order_sn}",'SN032144',$previewtmp);
		  $previewtmp=str_replace("{time}",time(),$previewtmp);
		  $previewtmp=str_replace("{good_line}",$good_line,$previewtmp);
		   $previewtmp=str_replace("{good_price}",'300',$previewtmp);
		    $previewtmp=str_replace("{dispatch_price}",'10',$previewtmp);
		      $previewtmp=str_replace("{order_price}",'310',$previewtmp);
		        $previewtmp=str_replace("{dispatch_realname}",'小王',$previewtmp);
		          $previewtmp=str_replace("{dispatch_tell}",'13500000001',$previewtmp);
		     $previewtmp=str_replace("{dispatch_address}",'上海市xxxx',$previewtmp);
	}
            
            
            
    require_once WEB_ROOT.'/includes/lib/phpmailer/PHPMailerAutoload.php';   
                  
             //******************** 配置信息 ********************************
	$smtpserver = $settings['smtp_server'];//SMTP服务器 
	$smtpauthmode =intval($settings['smtp_authmode']);//SMTP服务器端口
	
	$smtpserverport =intval($settings['smtp_port']);//SMTP服务器端口
	$smtpusermail = $settings['smtp_mail'];//SMTP服务器的用户邮箱
	$smtpemailto = $settings['smtp_to_mail'];//发送给谁
	$smtpuser = $settings['smtp_username'];//SMTP服务器的用户帐号
	$smtppass = $settings['smtp_passwd'];//SMTP服务器的用户密码
	$mailtitle =  $settings['smtp_title'];//邮件主题
	$mailcontent = $previewtmp;//邮件内容
	
	$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
	//************************ 配置信息 ****************************
	
	$mailer = new PHPMailer();
		$mailer->isSMTP();
		$mailer->CharSet = 'utf-8';
		$mailer->Host = $smtpserver ;
		$mailer->Port = $smtpserverport;
		$mailer->SMTPAuth = $smtpauthmode==1;
		$mailer->Username = $smtpuser;
		$mailer->Password = $smtppass;
		if($smtpauthmode==1)
		{
		$mailer->SMTPSecure = 'ssl';
		}
		$mailer->From = $smtpusermail ;
		$mailer->FromName = $smtpemailto;
		$mailer->isHTML(true);
		
			$mailer->Subject = $mailtitle;
	$mailer->Body = $previewtmp;
	$mailer->addAddress($smtpemailto);
$x=$mailer->send();  
    
    
    
            message('邮件已发送', 'refresh', 'success');
        }
		include page('noticemail');