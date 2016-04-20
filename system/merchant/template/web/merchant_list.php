<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>

<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>
<h3 class="header smaller lighter blue">商家列表</h3>
		<form action=""  class="form-horizontal" method="post">
	<table class="table table-striped table-bordered table-hover">
			<tbody >
				<tr>
				<td>
				<li style="float:left;list-style-type:none;">
						<select  style="margin-right:10px;margin-top:10px;width: 150px; height:34px; line-height:28px; padding:2px 0" name="district" >
							<option value="0">请选择区域</option>
							<?php  if(is_array($district)) { foreach($district as $row) { ?>
							<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GP['district']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
							<?php  } } ?>
						</select>
						
						</li>
						<li style="float:left;list-style-type:none;">
												<select name="status" style="margin-right:10px;margin-top:10px;width: 100px; height:34px; line-height:28px; padding:2px 0">
							<option value="1" selected>合作中</option>
							<option value="0" >不合作</option>
						</select>
						</li>
						
						<li style="float:left;list-style-type:none;">
											<span>关键字</span>	<input style="margin-right:10px;margin-top:10px;width: 300px; height:34px; line-height:28px; padding:2px 0" name="keyword" id="" type="text" value="<?php  echo $_GP['keyword'];?>">
						</li>
						<li style="list-style-type:none;">
						<button class="btn btn-primary" style="margin-right:10px;margin-top:10px;"><i class="icon-search icon-large"></i> 搜索</button>
						</li>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		
	<table class="table table-striped table-bordered table-hover">
  <tr >
     <th class="text-center" >编号</th>
    <th class="text-center">编号</th>
    <th class="text-center">商家名称</th>
	<th class="text-center" >所在区</th>
	<th class="text-center" >联系人</th>
	<th class="text-center" >电话</th>
    <th class="text-center" >状态</th>
    <th class="text-center" >操作</th>
  </tr>

		<?php  $t_index=1; if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td style="text-align:center;"><?php  echo $t_index++; ?></td>
                                     
                                        	<td style="text-align:center;"><?php  echo $item['no'];?></td>
											
											<td style="text-align:center;"><?php  echo $item['name'];?></td>
											
											<td style="text-align:center;"><?php  echo $item['district'];?></td>
											
											<td style="text-align:center;"><?php  echo $item['contact'];?></td>
											
											<td style="text-align:center;"><?php  echo $item['mobile'];?></td>
											
					
					
					<td style="text-align:center;"><?php  if($item['status']) { ?><span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-success" style="cursor:pointer;">合作中</span><?php  } else { ?><span data='<?php  echo $item['status'];?>' onclick="setProperty1(this,<?php  echo $item['id'];?>,'status')" class="label label-danger" style="cursor:pointer;">不合作</span><?php  } ?></td>
					<td style="text-align:center;">
					<a class="btn btn-xs btn-info" target="_blank" href="<?php echo WEBSITE_ROOT.mobile_url('detail',array('name'=>'merchantwap','id'=>$item['id']));?>"><i class="icon-eye-open"></i>&nbsp;查&nbsp;看&nbsp;</a>&nbsp;&nbsp;
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('merchant', array('id' => $item['id'], 'op' => 'post'))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;
						<a  class="btn btn-xs btn-info" href="<?php  echo web_url('merchant', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a></a>
					</td>
				</tr>
				<?php  } } ?>
 	
		</table>
		<?php  echo $pager;?>
<?php  include page('footer');?>
