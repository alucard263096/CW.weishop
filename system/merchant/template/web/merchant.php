<?php defined('SYSTEM_IN') or exit('Access Denied');?><?php  include page('header');?>
<h3 class="header smaller lighter blue"><?php  if(!empty($item['id'])) { ?>编辑<?php  }else{ ?>新增<?php  } ?>商家</h3>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>/addons/common/js/jquery-ui-1.10.3.min.js"></script>

 <form action="" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 商家代号：</label>

										<div class="col-sm-9">
													 <input type="text" name="merchantno" id="merchantno" maxlength="100" class="span7"  value="<?php  echo $item['no'];?>" />
										</div>
		</div>
 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 商家名称：</label>

										<div class="col-sm-9">
													 <input type="text" name="merchantname" id="merchantname" maxlength="100" class="span7"  value="<?php  echo $item['name'];?>" />
										</div>
		</div>
 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 联系人：</label>

										<div class="col-sm-9">
													 <input type="text" name="contact" id="contact" maxlength="100" class="span7"  value="<?php  echo $item['contact'];?>" />
										</div>
		</div>
 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 联系电话：</label>

										<div class="col-sm-9">
													 <input type="text" name="mobile" id="mobile" maxlength="100" class="span7"  value="<?php  echo $item['mobile'];?>" />
										</div>
		</div>
		
		 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 地区：</label>

										<div class="col-sm-9">
												  <select  style="margin-right:15px;" id="district" name="district"   autocomplete="off">
                <option value="0">地区</option>
                <?php  if(is_array($district)) { foreach($district as $row) { ?>
                <option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['district']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
                <?php  } } ?>
            </select>
										</div>
		</div>

 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 地址：</label>

										<div class="col-sm-9">
													 <input type="text" name="address" id="address" maxlength="100" class="span7"  value="<?php  echo $item['address'];?>" />
										</div>
		</div>
 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 商业圈：</label>

										<div class="col-sm-9">
													 <input type="text" name="business_loc" id="business_loc" maxlength="100" class="span7"  value="<?php  echo $item['business_loc'];?>" />
										</div>
		</div>
 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 坐标：</label>

										<div class="col-sm-9">
													 <input type="text" name="lat" id="lat" maxlength="100" class="span7"  value="<?php  echo $item['lat'];?>" placeholder="Lat" />
													 <input type="text" name="lng" id="lng" maxlength="100" class="span7"  value="<?php  echo $item['lng'];?>" placeholder="lng" />
										</div>
		</div>


 <div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 是否合作：</label>

										<div class="col-sm-9">
												 <input type="radio" name="status" value="1" id="isshow1" <?php  if($item['status'] == 1) { ?>checked="true"<?php  } ?> /> 是  &nbsp;&nbsp;
             <input type="radio" name="status" value="0" id="isshow2"  <?php  if($item['status'] == 0) { ?>checked="true"<?php  } ?> /> 否
										</div>
		</div>
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 商家属性：</label>

										<div class="col-sm-9">
				 <input type="checkbox" name="isrecommand" value="1" id="isrecommand" <?php  if($item['isrecommand'] == 1) { ?>checked="true"<?php  } ?> /> 首页推荐
                    &nbsp;   
										</div>
		</div>
		
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >主图：</label>

										<div class="col-sm-9">
				  <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;">
                        	 <?php  if(!empty($item['thumb'])) { ?>
                            <img src="<?php echo WEBSITE_ROOT;?>/attachment/<?php  echo $item['thumb'];?>" alt="" onerror="$(this).remove();">
                              <?php  } ?>
                            </div>
                        <div>
                         <input name="thumb" id="thumb" type="file" />
                            <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">移除图片</a>
                        </div>
                    </div>
										</div>
		</div>
		
		
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" > 其他图片：</label>

										<div class="col-sm-9">
				         <span id="selectimage" tabindex="-1" class="btn btn-primary"><i class="icon-plus"></i> 上传照片</span><span style="color:red;">
                    <input name="piclist" type="hidden" value="<?php  echo $item['piclist'];?>" /></span>
                <div id="file_upload-queue" class="uploadify-queue"></div>
                <ul class="ipost-list ui-sortable" id="fileList">
                    <?php  if(is_array($piclist)) { foreach($piclist as $v) { ?> 
                    <li class="imgbox" style="list-style-type:none;display:inline;  float: left;  position: relative;   width: 125px;  height: 130px;">
                        <span class="item_box">
                            <img src="<?php echo WEBSITE_ROOT;?>/attachment/<?php  echo $v['picurl'];?>" style="width:50px;height:50px">    </span>
                       		 <a  href="javascript:;" onclick="deletepic(this);" title="删除">删除</a>
                    
                        <input type="hidden" value="<?php  echo $v['picurl'];?>" name="attachment[]">
                    </li>
                    <?php  } } ?>
                </ul>
										</div>
		</div>
		
		
		
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >简单描述：</label>

										<div class="col-sm-9">
				   <textarea style="height:150px;"  id="description" name="description" cols="70"><?php  echo $item['description'];?></textarea>
             
										</div>
		</div>
		
		
				<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" >详细描述：</label>

										<div class="col-sm-9">
				    <textarea style="height:300px;" id="container" name="content" cols="70"><?php  echo $item['content'];?></textarea>
                  
										</div>
		</div>
		
      	<div class="form-group">
										<label class="col-sm-2 control-label no-padding-left" ></label>

										<div class="col-sm-9">
				    <button type="submit" class="btn btn-primary span2" name="submit" value="submit"><i class="icon-edit"></i>保存</button>    
										</div>
		</div>
		
 </form>


		
		
		
         
<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/themes/default/default.css" />
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/kindeditor-min.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/kindeditor/lang/zh_CN.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.config.js?x=1211"></script>
<script type="text/javascript" src="<?php echo RESOURCE_ROOT;?>addons/common/ueditor/ueditor.all.min.js?x=141"></script>
<script type="text/javascript">var ue = UE.getEditor('container');</script>
<script language="javascript">
		
$(function(){
	 
	var i = 0;
	$('#selectimage').click(function() {
		var editor = KindEditor.editor({
			allowFileManager : false,
			imageSizeLimit : '10MB',
			uploadJson : '<?php  echo web_url('upload')?>'
		});
		editor.loadPlugin('multiimage', function() {
			editor.plugin.multiImageDialog({
				clickFn : function(list) {
					if (list && list.length > 0) {
						for (i in list) {
							if (list[i]) {
								html =	'<li class="imgbox" style="list-style-type:none;display:inline;  float: left;  position: relative;  width: 125px;  height: 130px;">'+
								'<span class="item_box"> <img src="'+list[i]['url']+'" style="width:50px;height:50px"></span>'+
								'<a href="javascript:;" onclick="deletepic(this);" title="删除">删除</a>'+
								'<input type="hidden" name="attachment-new[]" value="'+list[i]['filename']+'" />'+
								'</li>';
								$('#fileList').append(html);
								i++;
							}
						}
						editor.hideDialog();
					} else {
						alert('请先选择要上传的图片！');
					}
				}
			});
		});
	});
});
function deletepic(obj){
	if (confirm("确认要删除？")) {
		var $thisob=$(obj);
		var $liobj=$thisob.parent();
		var picurl=$liobj.children('input').val();
		$.post('<?php  echo create_url('site',array('name' => 'merchant','do' => 'picdelete'))?>',{ pic:picurl},function(m){
			if(m=='1') {
				$liobj.remove();
			} else {
				alert("删除失败");
			}
		},"html");	
	}
}

    </script>
<?php  include page('footer');?>
