<?php
						$cfg=globaSetting();
							$member=get_member_account(true,true);
					$openid =$member['openid'] ;
					$memberinfo=member_get($openid);
			if (checksubmit("submit")) {
					if(empty($_GP['mobile']))
					{
							message("请输入手机号！");	
					}
					
							$data = array('realname' => $_GP['realname'],
                    'email' => $_GP['email']);
                    
					if(	$memberinfo['mobile']!=$_GP['mobile'])
					{
						$ckmember = mysqld_select("SELECT * FROM ".table('member')." where mobile=:mobile ", array(':mobile' => $_GP['mobile']));
						if(!empty($ckmember['openid']))
						{
								message($_GP['mobile']."已被注册。");	
						}
						$data['mobile']=$_GP['mobile'];
					}
		
				mysqld_update('member', $data,array('openid'=>$openid));
			
			  message('资料修改成功！', mobile_url('fansindex'), 'success');
			  
							}
		   include themePage('member');