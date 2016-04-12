<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">会员管理</h3>
<form action="" method="get" class="form-horizontal" enctype="multipart/form-data" >
				<input type="hidden" name="act" value="module" />
				<input type="hidden" name="name" value="member" />
				<input type="hidden" name="do" value="list" />
				<input type="hidden" name="mod" value="site"/>					
				
				       <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 会员姓名：</label>

										<div class="col-sm-9">
												<input name="realname" class="col-xs-10 col-sm-2"  type="text" value="<?php  echo $_GP['realname'];?>" />
										</div>
									</div>
									
										       <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 手机号码：</label>

										<div class="col-sm-9">
												<input name="mobile" type="text" class="col-xs-10 col-sm-2"  value="<?php  echo $_GP['mobile'];?>" />
										</div>
									</div>
												  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

										<div class="col-sm-9">
										<input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
										
										</div>
									</div>
									
				
			</form>
<h3 class="blue">	<span style="font-size:18px;"><strong>会员总数：<?php echo $total ?></strong></span></h3>
		
					<table class="table table-striped table-bordered table-hover">
			<thead >
				<tr>
					<th style="text-align:center;">手机号码</th>
					<th style="text-align:center;">会员姓名</th>
					<th style="text-align:center;">注册时间</th>
					<th style="text-align:center;">状态</th>
					<th style="text-align:center;">积分</th>
					<th style="text-align:center;">余额</th>
					<th style="text-align:center;">操作</th>
				</tr>
			</thead>
			<tbody>
 <?php  if(is_array($list)) { foreach($list as $v) { ?>
								<tr>
										<td class="text-center">
										<?php  echo $v['mobile'];?>
									</td>
									<td class="text-center">
										<?php  echo $v['realname'];?>
									</td>
									<td class="text-center">
										<?php  echo date('Y-m-d',$v['createtime'])?>
									</td>
									<td class="text-center">
									<?php  if($v['status']==0) { ?>
										<span class="label label-important">已禁用</span>
									<?php  } else { ?>
										<span class="label label-success">正常</span>
									<?php  } ?>
									</td>
										<td class="text-center">
												<?php  echo $v['credit'];?>
									</td>
									<td class="text-center">
										<?php  echo $v['gold'];?>
									</td>
									<td class="text-center">
											<?php  if($v['status']==1) { ?>
									<a class="btn btn-xs btn-danger" href="<?php  echo web_url('delete',array('name'=>'member','openid' => $v['openid'],'status' => 0));?>" onclick="return confirm('确定要禁用该账户吗？');"><i class="icon-edit"></i>禁用账户</a>
										
											<?php  } else { ?>
										<a class="btn btn-xs btn-success" href="<?php  echo web_url('delete',array('name'=>'member','openid' => $v['openid'],'status' => 1));?>" onclick="return confirm('确定要恢复该账户吗？');"><i class="icon-edit"></i>恢复账户</a>
										
									<?php  } ?>
										&nbsp;&nbsp;<a  class="btn btn-xs btn-info" href="<?php  echo web_url('detail',array('name'=>'member','openid' => $v['openid']));?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
										<a class="btn btn-xs btn-info" href="<?php  echo web_url('recharge',array('name'=>'member','openid' => $v['openid'],'op'=>'credit'));?>"><i class="icon-edit"></i>积分充值</a>&nbsp;&nbsp;
										<a class="btn btn-xs btn-info" href="<?php  echo web_url('recharge',array('name'=>'member','openid' => $v['openid'],'op'=>'gold'));?>"><i class="icon-edit"></i>余额充值</a>	
									</td>
								</tr>
								<?php  } } ?>
  </tbody>
    </table>
		<?php  echo $pager;?>
<?php  include page('footer');?>