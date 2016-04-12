<?php

   $member=get_member_account(true,true);
			$openid = $member['openid'];
       $member=member_get($openid);

    $award_info = mysqld_select("SELECT * FROM " . table('addon7_award')." where id=:id",array(":id"=>intval($_GP['award_id'])));
 
 if($member['credit']<$award_info['credit_cost'])
 {
 	message("积分不足无法兑换，您的积分：".$member['credit']."，兑换所需积分".$award_info['credit_cost']);
 }
 if($award_info['awardtype']==1)
 {
 		 $insert=array(
  	 	'openid' => $openid,
  	 		'realname' => '',
  	 		'mobile' => $member['mobile'],
  	 'status' => 0,
  	  'address' => '',
  	  		'createtime' => time(),
  	  		"award_id"=>intval($_GP['award_id'])
  	 );
  	 	member_credit($openid,$award_info['credit_cost'],'usecredit','积分兑换消费积分,兑换id:'.intval($_GP['award_id']));
  	 
  	   mysqld_insert('addon7_request', $insert);
  	     message('提交成功', mobile_url('index'), 'success');
 	
 }
 
 
	 $setting = mysqld_select("SELECT * FROM " . table('addon7_config') );
   $request_info = mysqld_select("SELECT * FROM " . table('addon7_request')." where openid=:openid order by id desc limit 1",array(":openid"=>$openid));
 
  $address=$request_info['address'];
   $mobile=$request_info['mobile'];
   $realname=$request_info['realname'];
  if(empty($realname))
  {
  $realname=$member['realname'];
	}
	 if(empty($mobile))
  {
  $mobile=$member['mobile'];
	}
	
	 if (checksubmit("submit")) {
	 	 $award_info = mysqld_select("SELECT * FROM " . table('addon7_award')." where id=:id",array(":id"=>intval($_GP['award_id'])));
 if(!empty($award_info['id']))
 {
	 $insert=array(
  	 	'openid' => $openid,
  	 		'realname' => $_GP['realname'],
  	 		'mobile' => $_GP['mobile'],
  	 'status' => 0,
  	  'address' => $_GP['address'],
  	  		'createtime' => time(),
  	  		"award_id"=>intval($_GP['award_id'])
  	 );
  	 
  	   mysqld_insert('addon7_request', $insert);
  	     message('提交成功', mobile_url('index'), 'success');
	}
	}
  
 include addons_page('useaward');