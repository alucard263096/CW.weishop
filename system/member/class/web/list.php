<?php
		  $pindex = max(1, intval($_GP['page']));
      $psize = 30;
      $condition='';
      $conditiondata=array();
      if(!empty($_GP['realname']))
      {
      	
      	 $condition=' and realname like :realname';
      	 $conditiondata[':realname']='%'.$_GP['realname'].'%';
      }
         if(!empty($_GP['mobile']))
      {
      	
      	 $condition=' and mobile like :mobile';
      	 $conditiondata[':mobile']='%'.$_GP['mobile'].'%';
      }
			$list = mysqld_selectall('SELECT * FROM '.table('member')." where 1=1 and `istemplate`=0  $condition "." LIMIT " . ($pindex - 1) * $psize . ',' . $psize,$conditiondata);
	 		$total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('member')." where 1=1 and `istemplate`=0   $condition",$conditiondata);
      $pager = pagination($total, $pindex, $psize);
			include page('list');