<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>百家CMS安装程序</title>
<link href="<?php echo RESOURCE_ROOT;?>/addons/modules/install.css?x=20150530_1" rel="stylesheet" />
</head>
<body>
	<?php if( $op=="display"){?>
<table width="830" border="0" align="center" cellpadding="0" cellspacing="0" >
  <tr>
    <td align="center">〓 许可协议 〓</td>
  </tr>
  <tr>
    <td>
	<div class="pact">
            <p>本软件的版权归 百家CMS官方 所有，且受《中华人民共和国计算机软件保护条例》等知识产权法律及国际条约与惯例的保护。<p>
            <p>本协议适用且仅适用于 百家CMS 版本，百家CMS官方拥有对本协议的最终解释权。<p>
            <p>无论个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，在理解、同意、并遵守本协议的全部条款后，方可开始使用本软件。<p>
            <h4><strong>一、协议许可和限制</strong></h4>
            <ol>
              <p>1、未经作者书面许可，不得衍生出私有软件。<p>
              <p>2、不管首页是否以百家CMS系统生成，网站首页最下面保留清晰可见的支持信息并链接到百家CMS站，不得以友情链接等方式代替，若网站性质等因素所限，不适合保留支持信息，请和作者签订《百家CMS授权合同》<p>
              <p>3、您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。<p>
              <p>4、未经官方许可，禁止在 百家CMS 的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发<p>
              <p>5、您将本软件应用在商业用途时，需遵守以下几条：
      <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1、使用本软件建设网站时，无需支付使用费用，但需保留百家CMS支持链接信息。<p>
                  <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2、本源码仅学习交流，如需商用，请和百家CMS联系授权。<p>
                  <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3、若网站性质等因素所限，不适合保留支持信息，请与作者联系取得书面授权。<p>
				  <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4、使用者所生成的网站，首页要包含软件的版权信息；不得对后台版权进行修改。<p>
              <p>
            <h4><strong>二、有限担保和免责声明</strong></h4>
              <p>1、本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。<p>
              <p>2、用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。<p>
              <p>3、百家CMS官方不对使用本软件构建的网站中的内容承担责任。<p>
            <br>
            <p>本协议保留作者的版权信息在许可协议文本之内，不得擅自修改其信息。</p>
           &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;协议著作权所有 &copy; baijiacms.com&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; 软件版权所有 &copy; baijiacms.com</p>
            <p>
	</td>
  </tr>
  <tr>
    <td>
<div class="readpact boxcenter">
	<input name="readpact" type="checkbox" id="readpact" value="" /><label for="readpact"><strong>我已经阅读并同意此协议</strong></label>
</div>
<div class="butbox boxcenter">
	<input type="button" class="nextbut" value="" onclick="document.getElementById('readpact').checked ?window.location.href='<?php echo web_url("install",array("name"=>"modules","op"=>"setp2"))?>' : alert('您必须同意软件许可协议才能安装！');" />
</div>
	</td>
  </tr>
</table>
<?php }?>
<?php if( $op=="setp2"){?>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0"  class="twbox"  style="font-size:23px">
    <tr  >
      <td colspan="2" align="center"><strong style="font-size:23px">程序依赖环境检查</strong></td>
    </tr>
    
	  <tr>
      <td width="260" align="right">mysql_connect：</td>
      <td><?php echo $resultarray['mysql_connect']; ?>
	  </td>
    </tr>
      	<tr>
      <td width="260" align="right">pdo_mysql：</td>
      <td><?php echo $resultarray['pdo_mysql']; ?>
	  </td>
    </tr>
	<tr>
      <td width="260" align="right">allow_url_fopen：</td>
      <td><?php echo $resultarray['allow_url_fopen']; ?>
	  </td>
    </tr>
    	<tr>
      <td width="260" align="right">file_get_contents：</td>
      <td><?php echo $resultarray['file_get_contents']; ?>
	  </td>
    </tr>
    	<tr>
      <td width="260" align="right">fsockopen：</td>
      <td><?php echo $resultarray['fsockopen']; ?>
	  </td>
    </tr>
    	<tr>
      <td width="260" align="right">xml_parser_create：</td>
      <td><?php echo $resultarray['xml_parser_create']; ?>
	  </td>
    </tr>
   	<tr>
      <td width="260" align="right">curl_init：</td>
      <td ><?php echo $resultarray['curl_init']; ?>
	  </td>
    </tr>

    <tr>
      <td colspan="2" align="right">
	  <div class="butbox boxcenter">
	  	<input name="doact" type="hidden"  value="install"  />
	<input type="button" class="backbut" value="" onclick="window.location.href='<?php echo web_url("install",array("name"=>"modules","op"=>"display"))?>';" style="margin-right:20px" />
	<?php if( $allcheck){?>
	<input type="button" class="nextbut" value="" onclick="window.location.href='<?php echo web_url("install",array("name"=>"modules","op"=>"setp3"))?>';" />
<?php }else{?><br/>
请解决环境问题后，刷新页面，进行下一步操作。
<?php }?>
</div></td>
    </tr>


<?php }?>
	<?php if( $op=="setp3"){?>
	
<form id="form1" name="form1" method="post" >
  <table width="700" border="0" align="center" cellpadding="0" cellspacing="0"  class="twbox">
    <tr>
      <td colspan="2" align="center">百家CMS 正式版-系统安装-请仔细阅读说明,然后进行安装</td>
    </tr>

	
	<tr>
      <td width="260" align="right"><strong>数据库地址：</strong></td>
      <td><input name="dbhost" type="text" class="textipt" id="dbhost" style="width:150px" value="127.0.0.1" />
	  </td>
    </tr>
    
    <tr>
      <td width="260" align="right"><strong>数据库端口：</strong></td>
      <td><input name="dbport" type="text" class="textipt" id="dbport" style="width:150px" value="3306" />
	  </td>
    </tr>
	<tr>
      <td width="260" align="right"><strong>数据库名称：</strong></td>
      <td><input name="dbname" type="text" class="textipt" id="dbname" style="width:150px" value="" />
      数据库名称
	  </td>
    </tr>

	<tr>
      <td width="260" align="right"><strong>数据库用户名：</strong></td>
      <td><input name="dbuser" type="text" class="textipt" id="dbuser" style="width:150px" value="root" />
      数据库连接账号
	  </td>
    </tr>


	<tr>
      <td width="260" align="right"><strong>数据库密码：</strong></td>
      <td><input name="dbpwd" type="text" class="textipt" id="dbpwd" style="width:150px" value="" />
      数据库连接密码
	  </td>
    </tr>

	<tr>
      <td width="260" align="right"><strong>登录帐号：</strong></td>
      <td><input name="adminname" type="text" id="adminname" class="textipt" value="admin"  style="width:150px" /></td>
    </tr>
    <tr>
      <td align="right"><strong>登录密码：</strong></td>
      <td><input name="adminpwd" type="text"  class="textipt" value="" style="width:150px" /></td>
    </tr>



    <tr>
      <td colspan="2" align="right">
	  <div class="butbox boxcenter">
	  	<input name="doact" type="hidden"  value="install"  />
	<input type="button" class="backbut" value="" onclick="window.location.href='<?php echo web_url("install",array("name"=>"modules","op"=>"setp2"))?>';" style="margin-right:20px" />
	<input name="submit" type="submit" class="setupbut"    value="" />
</div></td>
    </tr>
  </table>
</form>

	<?php }?>
</body>
</html>
