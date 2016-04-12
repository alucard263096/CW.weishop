<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue">商城基础设置</h3>
<style>
	.good_line_table{
		
		width:100%;
		}
	</style>
<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" >
	   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 商店名称：</label>

										<div class="col-sm-9">
												  <input type="text" name="shop_title" class="col-xs-10 col-sm-2" value="<?php  echo $settings['shop_title'];?>" />
										</div>
									</div>
									
										   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 备案号：</label>

										<div class="col-sm-9">
												  <input type="text" name="shop_icp" class="col-xs-10 col-sm-2" value="<?php  echo $settings['shop_icp'];?>" />
										</div>
									</div>
									
								<!--		   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 商店描述：</label>

										<div class="col-sm-9">
												 <input type="text" name="shop_description" class="col-xs-10 col-sm-2" value="<?php  echo $settings['shop_description'];?>" />
										</div>
									</div>
									
											   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 商店关键字：</label>

										<div class="col-sm-9">
												 <input type="text" name="shop_keyword" class="col-xs-10 col-sm-2" value="<?php  echo $settings['shop_keyword'];?>" />
										</div>
									</div>-->
									
										   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 商店 Logo：</label>

										<div class="col-sm-9">
											
											<div class="fileupload fileupload-new" data-provides="fileupload">
			                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
			                        	 <?php  if(!empty($settings['shop_logo'])) { ?>
			                            <img style="width:100%" src="<?php echo WEBSITE_ROOT;?>/attachment/<?php  echo $settings['shop_logo'];?>" alt="" onerror="$(this).remove();">
			                              <?php  } ?>
			                            </div>
			                        <div>
			                         <input name="shop_logo" id="shop_logo" type="file"  />
			                            <a href="#" class="fileupload-exists" data-dismiss="fileupload">移除图片</a>
			                        </div>
			                    </div>
											
										</div>
									</div>
									
									
										   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 是否开启注册：</label>

										<div class="col-sm-9">
												   <input type="radio" name="shop_openreg" value="0" id="shop_closereg" <?php  if($settings['shop_openreg'] == 0) { ?>checked="true"<?php  } ?> /> 关闭  &nbsp;&nbsp;
             
              		  <input type="radio" name="shop_openreg" value="1" id="shop_closereg"  <?php  if($settings['shop_openreg'] == 1) { ?>checked="true"<?php  } ?> /> 开启
             
										</div>
									</div>
					
									
									
									   <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 帮助说明：</label>

										<div class="col-sm-9">
											<textarea name="help" id="help" cols="60" rows="8"><?php  echo $settings['help'];?></textarea>
											</div>
									</div>
										
													
											  <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" for="form-field-1"> </label>

										<div class="col-sm-9">
										<br/><input name="submit" type="submit" value=" 提 交 " class="btn btn-info"/>
										
		                     </div>
		                     </div>
				
</form>

			<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.config.js?x=1211"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.all.min.js?x=141"></script>
<script type="text/javascript">var ue = UE.getEditor('help');</script>

<?php  include page('footer');?>