<?php
		    	$member=get_member_account(false);
				$member=member_get($member['openid']);
			if(empty($member['openid']))
				{
				$member=get_member_account(false);	
				$member['createtime']=time();
				}
			$is_login=is_login_account();
					$cfg=globaSetting();
			$weixinfans=get_weixin_fans_byopenid($member['openid'],$member['openid']);
			if(!empty($weixinfans)&&!empty($weixinfans['avatar']))
			{
				$avatar=$weixinfans['avatar'];
			}
			
			include themePage('fansindex');