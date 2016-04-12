<?php
defined('SYSTEM_IN') or exit('Access Denied');
define('subscribe_key', '系统_关注事件');
define('default_key', '系统_默认回复');
class weixinAddons  extends BjModule {



		public function do_Rule()
	{
			global $_GP;
			hasrule('weixin','weixin');
        $operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
           
        if($operation=='detail')
        {
        	
        	if(!empty($_GP['id']))
        	{
        	$rule = mysqld_select('SELECT * FROM '.table('weixin_rule')." WHERE  id = :id" , array(':id' =>intval($_GP['id'])));
        	}
        	
                
    			if(checksubmit())
    			{
    	
    				if(empty($_GP['id']))
    				{
    								$count = mysqld_selectcolumn('SELECT count(id) FROM '.table('weixin_rule')." WHERE  keywords = :keywords" , array(':keywords' =>$_GP['keywords']));
					if($count>0)
					{
						message('触发关键字'.$_GP['keywords']."已存在！");
					}
    				 	if (!empty($_FILES['thumb']['tmp_name'])) {
                    file_delete($_GP['thumb_old']);
                    $upload = file_upload($_FILES['thumb']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $thumb = $upload['path'];
                }
    				
    					$data=array('title'=>$_GP['title'],'ruletype'=>$_GP['ruletype'],'keywords'=>$_GP['keywords'],'thumb'=>$thumb,'description'=>$_GP['description'],'url'=>$_GP['url']);
          	 mysqld_insert('weixin_rule', $data);
    					message('保存成功！', 'refresh', 'success');
    				}else
    				{
    					if($rule['keywords']!=$_GP['keywords'])
    					{
    									$count = mysqld_selectcolumn('SELECT count(id) FROM '.table('weixin_rule')." WHERE  keywords = :keywords" , array(':keywords' =>$_GP['keywords']));
					if($count>0)
					{
						message('触发关键字'.$_GP['keywords']."已存在！");
					}
    						
    					}
    					
    					 	if (!empty($_FILES['thumb']['tmp_name'])) {
                    file_delete($_GP['thumb_old']);
                    $upload = file_upload($_FILES['thumb']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $thumb = $upload['path'];
                }
    						$data=array('title'=>$_GP['title'],'ruletype'=>$_GP['ruletype'],'keywords'=>$_GP['keywords'],'description'=>$_GP['description'],'url'=>$_GP['url']);
          	
          	if(!empty($thumb))
          	{
          		$data['thumb']=$thumb;
          		
          	}
          	 mysqld_update('weixin_rule', $data, array('id' => $_GP['id']));
    					
    					message('修改成功！', 'refresh', 'success');
    				}
    			}
    				include page('rule_detail');
    				exit;
        }
        if($operation=='delete'&&!empty($_GP['id']))
        {
        		
        	 mysqld_delete('weixin_rule', array('id'=>$_GP['id']));
        	 message('删除成功！', 'refresh', 'success');
        }
        
				 
        $list=mysqld_selectall('SELECT * FROM '.table('weixin_rule'));
			include page('rule');
	}

	
	public function do_Menumodify() {
			global $_GP;
			hasrule('weixin','weixin');
			$menu=$_GP['menudate'];
		$return= $this->menuCreate($menu);
		if($return==true)
		{
		message('修改成功', 'refresh', 'success');	
		}else
		{
						message($return);	
			}

	}
	
	public function do_Designer()
	{
		hasrule('weixin','weixin');
		
		$menus = $this->menuQuery();
		
		if(is_array($menus['menu']['button'])) {
	foreach($menus['menu']['button'] as &$m) {
		if(isset($m['url'])) {
			$m['url'] = urldecode($m['url']);
		}
		if(isset($m['key'])) {
			$m['forward'] = $m['key'];
		}
		if(is_array($m['sub_button'])) {
			foreach($m['sub_button'] as &$s) {
				if(isset($s['url'])){
					$s['url']=urldecode($s['url']);
				}
				$s['forward'] = $s['key'];
			}
		}
	}
}
		
			include page('designer');
	}
		private function menuResponseParse($content) {
		if(empty($content)) {
			return message( "接口调用失败，请重试！公众平台返回错误信息: {$content}" );
		}
		$dat = $content;
		$result = @json_decode($dat, true);
		if(is_array($result) && $result['errcode'] == '0') {
			return true;
		} else {
			if(is_array($result)) {
				return  message("微信公众平台返回错误. 错误代码: {$result['errcode']} 错误信息: {$result['errmsg']} 错误描述: " . $this->error_code($result['errcode']));
			} else {
				return  message('微信公众平台未知错误');
			}
		}
	}
	
	
	private function error_code($code) {
		$errors = array(
			'-1' => '系统繁忙',
			'0' => '请求成功',
			'40001' => '获取access_token时AppSecret错误，或者access_token无效',
			'40002' => '不合法的凭证类型',
			'40003' => '不合法的OpenID',
			'40004' => '不合法的媒体文件类型',
			'40005' => '不合法的文件类型',
			'40006' => '不合法的文件大小',
			'40007' => '不合法的媒体文件id',
			'40008' => '不合法的消息类型',
			'40009' => '不合法的图片文件大小',
			'40010' => '不合法的语音文件大小',
			'40011' => '不合法的视频文件大小',
			'40012' => '不合法的缩略图文件大小',
			'40013' => '不合法的APPID',
			'40014' => '不合法的access_token',
			'40015' => '不合法的菜单类型',
			'40016' => '不合法的按钮个数',
			'40017' => '不合法的按钮个数',
			'40018' => '不合法的按钮名字长度',
			'40019' => '不合法的按钮KEY长度',
			'40020' => '不合法的按钮URL长度',
			'40021' => '不合法的菜单版本号',
			'40022' => '不合法的子菜单级数',
			'40023' => '不合法的子菜单按钮个数',
			'40024' => '不合法的子菜单按钮类型',
			'40025' => '不合法的子菜单按钮名字长度',
			'40026' => '不合法的子菜单按钮KEY长度',
			'40027' => '不合法的子菜单按钮URL长度',
			'40028' => '不合法的自定义菜单使用用户',
			'40029' => '不合法的oauth_code',
			'40030' => '不合法的refresh_token',
			'40031' => '不合法的openid列表',
			'40032' => '不合法的openid列表长度',
			'40033' => '不合法的请求字符，不能包含\uxxxx格式的字符',
			'40035' => '不合法的参数',
			'40038' => '不合法的请求格式',
			'40039' => '不合法的URL长度',
			'40050' => '不合法的分组id',
			'40051' => '分组名字不合法',
			'41001' => '缺少access_token参数',
			'41002' => '缺少appid参数',
			'41003' => '缺少refresh_token参数',
			'41004' => '缺少secret参数',
			'41005' => '缺少多媒体文件数据',
			'41006' => '缺少medSYSTEM_id参数',
			'41007' => '缺少子菜单数据',
			'41008' => '缺少oauth code',
			'41009' => '缺少openid',
			'42001' => 'access_token超时',
			'42002' => 'refresh_token超时',
			'42003' => 'oauth_code超时',
			'43001' => '需要GET请求',
			'43002' => '需要POST请求',
			'43003' => '需要HTTPS请求',
			'43004' => '需要接收者关注',
			'43005' => '需要好友关系',
			'44001' => '多媒体文件为空',
			'44002' => 'POST的数据包为空',
			'44003' => '图文消息内容为空',
			'44004' => '文本消息内容为空',
			'45001' => '多媒体文件大小超过限制',
			'45002' => '消息内容超过限制',
			'45003' => '标题字段超过限制',
			'45004' => '描述字段超过限制',
			'45005' => '链接字段超过限制',
			'45006' => '图片链接字段超过限制',
			'45007' => '语音播放时间超过限制',
			'45008' => '图文消息超过限制',
			'45009' => '接口调用超过限制',
			'45010' => '创建菜单个数超过限制',
			'45015' => '回复时间超过限制',
			'45016' => '系统分组，不允许修改',
			'45017' => '分组名字过长',
			'45018' => '分组数量超过上限',
			'46001' => '不存在媒体数据',
			'46002' => '不存在的菜单版本',
			'46003' => '不存在的菜单数据',
			'46004' => '不存在的用户',
			'47001' => '解析JSON/XML内容错误',
			'48001' => 'api功能未授权',
			'50001' => '用户未授权该api',
		);
		$code = strval($code);
	if($code == '40001') {
					$rec = array();
					$rec['access_token'] = '';
         refreshSetting($rec);
			
			return '微信公众平台授权异常, 系统已修复这个错误, 请刷新页面重试.';
		}
		if($errors[$code]) {
			return $errors[$code];
		} else {
			return '未知错误';
		}
	}
	
	


		public function menuCreate($domenu) {
		if($domenu==']')
		{
			return $this->menuDelete();
		}
					$mDat = $domenu;
		$mDat = htmlspecialchars_decode($mDat);
		$menu = json_decode($mDat, true);
		/*如果不使用历史记录，需要将菜单按钮URL编码*/
		foreach($menu as &$m) {
			$m['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $m['name']);
			$m['name'] = urlencode($m['name']);
			if(isset($m['url']) && !empty($m['url'])){
				$m['url'] = $this->smartUrlEncode($m['url']);
			}
			if(is_array($m['sub_button'])) {
				foreach($m['sub_button'] as &$s) {
					$s['name'] = preg_replace_callback('/\:\:([0-9a-zA-Z_-]+)\:\:/', create_function('$matches', 'return utf8_bytes(hexdec($matches[1]));'), $s['name']);
					$s['name'] = urlencode($s['name']);
					if(!empty($s['url'])){
						$s['url'] = $this->smartUrlEncode($s['url']);
					}
				}
			}
		}
			
			if(!is_array($menu)) {
		message('操作非法，自定义菜单结构错误！');
	}
			$menus = array();
		$menus['button'] = $menu;
	 	$dat = json_encode($menus);
		$dat = urldecode($dat);
		$token = get_weixin_token();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$token}";
		$content = http_post($url, $dat);
		return $this->menuResponseParse($content);
	}
function smartUrlEncode($url){
	if (strpos($url, '=') === false) {
		return $url;
	} else {
		$urls = parse_url($url);
		parse_str($urls['query'], $queries);
		if (!empty($queries)) {
			foreach ($queries as $variable => $value) {
				$params[$variable] = urlencode($value);
			}
		}
		$queryString = http_build_query($params, '', '&');
		return $urls['scheme'] . '://' . $urls['host'] . $urls['path'] . '?' . $queryString . '#' . $urls['fragment'];
	}
}
	public function menuDelete() {
		$token = get_weixin_token();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token={$token}";
		$content = http_get($url);
		return $this->menuResponseParse($content);
	}

	public function menuModify($menu) {
		return $this->menuCreate($menu);
	}

	public function menuQuery() {
		$token = get_weixin_token();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$token}";
		$content = http_get($url);
		if(empty($content)) {
			return message( "接口调用失败，请重试！微信公众平台返回错误信息: {$content}" );
		}
		$dat = $content;
		$result = @json_decode($dat, true);
		if(is_array($result) && !empty($result['menu'])) {
			return $result;
		} else {
			if($result['errcode']!='46003')
			{
			if(is_array($result)) {
				return message( "微信公众平台返回接口错误。错误信息: {$result['errmsg']} 错误描述: " . $this->error_code($result['errcode']));
			} else {
				return message( '微信公众平台未知错误');
			}
		}
		}
	}
	function error($code, $msg = '') {
	return array(
		'errno' => $code,
		'message' => $msg,
	);
}

public function do_Remove()
	{
		hasrule('weixin','weixin');
			$ret = $this->menuDelete();
		if(is_error($ret)) {
			message($ret['message'], 'refresh');
		} else {
			message('已经成功删除菜单，请重新创建。', 'refresh');
		}
	}
	
	public function do_Refresh()
	{
		message('', 'refresh');
	}
	
	public function do_Setting()
	{
		hasrule('weixin','weixin');
				global $_GP;
$settings=globaSetting(array(
               'weixinname',
                'weixintoken' ,
                'EncodingAESKey',
						  	'weixin_appId' ,
				   		  'weixin_appSecret' ));
				   		  
		$payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE code = :code", array(':code' => 'weixin'));
		if(!empty($payment['configs']))
		{
			$paymentconfig = unserialize($payment['configs']);			   		
		}	   		
				$thirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE code = :code", array(':code' => 'weixin'));
				   		
 if (checksubmit()) {
            $cfg = array(
               'weixinname' => $_GP['weixinname'],
                'weixintoken' => $_GP['weixintoken'],
                'EncodingAESKey' => $_GP['EncodingAESKey'],
						  	'weixin_appId' => $_GP['weixin_appId'],
				   		  'weixin_appSecret' => $_GP['weixin_appSecret']
            );
        
         
          refreshSetting($cfg);
 					  mysqld_delete('config', array('name'=>'weixin_access_token'));
         
         $settings=globaSetting(array(
               'weixinname',
                'weixintoken' ,
                'EncodingAESKey',
						  	'weixin_appId' ,
				   		  'weixin_appSecret' ));
				   		  
				   		  
				   		  
				   		      $thirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE code = :code", array(':code' => 'weixin'));
              	require WEB_ROOT.'/system/modules/plugin/thirdlogin/weixin/lang.php';
              
                 if (empty($thirdlogin['id'])) {
                 		 $data = array(
                    'code' => 'weixin',
                     'enabled' => intval($_GP['thirdlogin_weixin']),
                    'name' => $_LANG['thirdlogin_weixin_name']
                  );
									 mysqld_insert('thirdlogin', $data);
                } else {
	                		 $data = array(
	                		  'enabled' => intval($_GP['thirdlogin_weixin']),
	                    'name' => $_LANG['thirdlogin_weixin_name'],
	                  );
                    mysqld_update('thirdlogin',$data , array('code' =>'weixin'));
                }
				   		  
				   		  
            if(empty($settings['weixintoken'])&&!empty($_GP['weixintoken']))
	        {
	        	header("location:". create_url('site', array('name' => 'weixin','do' => 'setting')));
	        }else
	        {
            message('保存成功', 'refresh', 'success');
          }
        }
        if(empty($settings['weixintoken']))
        {
        $isfirst=true;	
        }

					include page('setting');
	}
}


