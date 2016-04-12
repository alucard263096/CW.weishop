<?php
		$member = mysqld_select('SELECT * FROM '.table('member').' where openid=:openid', array(':openid' => $_GP['openid']));
	 	$weixininfo=mysqld_select('SELECT * FROM '.table('weixin_wxfans').' where openid=:openid', array(':openid' => $_GP['openid']));
		
     if (checksubmit('submit')) {
	     	if(!empty($member['openid']))
	     	{
	     	
	     	}
	     			if($member['mobile']!=$_GP['mobile'])
			{
			
				$checkmember = mysqld_select('SELECT * FROM '.table('member').' where mobile=:mobile', array(':mobile' => $_GP['mobile']));
		 		if(!empty($checkmember['openid']))
		 		{
					message($_GP['mobile']."已被注册。");	
				}
			}
	     		$datas=array('realname'=> $_GP['realname'],'mobile'=> $_GP['mobile'],'email'=> $_GP['email']);
	     	if(!empty($_GP['password']))
	     	{
	     			if($_GP['password']==$_GP['repassword'])
			     	{
			     		$datas['pwd']=md5($_GP['password']);
			     	}else
			     	{
			     		
			     		message("两次密码不相同");
			     		}
	     		
	     	}
              mysqld_update('member', $datas, array('openid' => $_GP['openid']));
	     		   message('操作成功！', 'refresh', 'success');
	     	}
     	
	include page('detail');