<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">配送方式</h3>

<div style="margin:10px">
<a href="<?php  echo create_url('site', array('name' => 'shop','do' => 'dispatch','op'=>'post'))?>" class="btn btn-primary">新增配送方式</a>
</div>
	<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
				 <th class="text-center" >配送名称</th>
    <th class="text-center" >配送类型</th>
    <th class="text-center" >显示优先级</th>
    <th class="text-center" >操作</th>
				</tr>
			</thead>
			<tbody>
		<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td class="text-center"><?php  echo $item['dispatchname'];?></td>
          <td class="text-center"><?php   if($item['sendtype']==0){?>快递<?php } ?><?php   if($item['sendtype']==1){?>自提<?php } ?></td>
          <td class="text-center"><?php  echo $item['displayorder'];?></td>
         <td class="text-center"><a class="btn btn-xs btn-info"  href="<?php  echo web_url('dispatch', array('id' => $item['id'], 'op' => 'post'))?>"><i class="icon-edit"></i>&nbsp;编&nbsp;辑&nbsp;</a>&nbsp;&nbsp;<a class="btn btn-xs btn-danger"  href="<?php  echo web_url('dispatch', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;"><i class="icon-edit"></i>&nbsp;删&nbsp;除&nbsp;</a></td>
				</tr>
				<?php  } } ?>
  			</tbody>
  		</table>
	<?php  echo $pager;?>

<?php  include page('footer');?>
