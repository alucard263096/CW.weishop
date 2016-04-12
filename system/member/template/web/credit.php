<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">积分充值</h3>


<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" >
<input type="hidden" name="op" value="<?php  echo $op;?>">
<input type="hidden" name="openid" value="<?php  echo $member['openid'];?>">

   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 会员姓名：</label>

										<div class="col-sm-9">
													<?php  echo $member['realname'];?>
										</div>
									</div>
									
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 联系电话：</label>

										<div class="col-sm-9">
													<?php  echo $member['mobile'];?>
										</div>
									</div>
							<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 现有积分：</label>

										<div class="col-sm-9">
                      <?php  echo $member['credit'];?>
										</div>
									</div>
	
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 充值备注：</label>

										<div class="col-sm-9">
                     <input type='text' name='remark' maxlength="20" class="col-xs-10 col-sm-2" value="后台积分充值" />  
										</div>
									</div>
									
													<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 充值积分：</label>

										<div class="col-sm-9">
                 <input type='text' name='fee' maxlength="20" class="col-xs-10 col-sm-2" value="" /> 
										</div>
									</div>
									
									
											  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

										<div class="col-sm-9">
										<input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
										
										</div>
									</div>
	
		
	</form>
	
	<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
				   <th class="text-center" >时间</th>
    <th class="text-center" >类型</th>
    <th class="text-center">积分</th>
    <th class="text-center" >备注</th>
				</tr>
			</thead>
			<tbody>
   <?php  if(is_array($list)) { foreach($list as $v) { ?>
   
   <tr>
   <td class="text-center"><?php  echo date('Y-m-d H:i:s',$v['createtime'])?></td>
    <td class="text-center"><?php  echo $v['type']=='addcredit'?'充值':($v['type']=='usecredit'?'消费':'')?></td>
     <td class="text-center"><?php  echo $v['fee']?></td>
      <td class="text-center"><?php  echo $v['remark']?></td>
    </tr>
  			<?php  } } ?>
  			</tbody>
  		</table>
  
<?php  include page('footer');?>