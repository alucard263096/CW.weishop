<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.baijiacms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 百家威信 <QQ:2752555327> <http://www.baijiacms.com>
// +----------------------------------------------------------------------
define('WEB_ROOT', str_replace("\\",'/', dirname(dirname(__FILE__))));
if(file_exists(WEB_ROOT.'/config/version.php'))
{
	require WEB_ROOT.'/config/version.php';
}
define('SAPP_NAME', '百家CMS');
define('CORE_VERSION', 20150710);
defined('SYSTEM_VERSION') or define('SYSTEM_VERSION', CORE_VERSION);
header('Content-type: text/html; charset=UTF-8');
define('SYSTEM_WEBROOT', WEB_ROOT);
define('TIMESTAMP', time());
define('SYSTEM_IN', true);
$document_root = basename($_SERVER["DOCUMENT_ROOT"]);
$dir = basename(dirname(dirname(__FILE__)));
$s_webroot='/';
if ($document_root != $dir) {
$s_webroot='/'.basename($dir).'/';
}
define('WEBSITE_ROOT', 'http://'.$_SERVER['HTTP_HOST'].$s_webroot);	
define('RESOURCE_ROOT', WEBSITE_ROOT.'assets/');
define('SYSTEM_ROOT', WEB_ROOT.'/system/');	
define('ADDONS_ROOT', WEB_ROOT.'/addons/');
define('DEVELOPMENT', 0);
define('SQL_DEBUG', 0);
session_start();
if(DEVELOPMENT) {
	ini_set('display_errors','1');
	error_reporting(E_ALL ^ E_NOTICE);
} else {
	error_reporting(0);
}
ob_start();

$_GP = $_CMS =  array();
$_GP = array_merge($_GET, $_POST, $_GP);
function irequestsplite($var) {
	if (is_array($var)) {
		foreach ($var as $key => $value) {
			$var[htmlspecialchars($key)] = irequestsplite($value);
		}
	} else {
		$var = str_replace('&amp;', '&', htmlspecialchars($var, ENT_QUOTES));
	}
	return $var;
}
$_GP = irequestsplite($_GP);
$modulename = $_GP['name'];
if(empty($modulename))
{
		if(empty($mname))
	{
	$modulename='public';	
	}else
	{
	$modulename=$mname;	
	}
}

if(empty($_GP['do']))
{
	if(empty($do))
	{
	$_GP['do']='index';	
	}else
	{
		$_GP['do']=$do;
	}
}
$pdo = $_CMS['pdo'] = null;
$bjconfigfile =  WEB_ROOT."/config/config.php";
$BJCMS_CONFIG=array();
if(file_exists($bjconfigfile))
{
require $bjconfigfile;
}
$bjconfig=$BJCMS_CONFIG;
if(empty($bjconfig['db']['host']))
{
$bjconfig['db']['host'] = '';
}
if(empty($bjconfig['db']['username']))
{
$bjconfig['db']['username'] = '';
}
if(empty($bjconfig['db']['password']))
{
$bjconfig['db']['password'] = '';	
}
if(empty($bjconfig['db']['port']))
{
$bjconfig['db']['port'] = '';
}
if(empty($bjconfig['db']['database']))
{
$bjconfig['db']['database'] = '';
}
$bjconfig['db']['charset'] = 'utf8';
$_CMS['config'] = $bjconfig;
$_CMS['module']=$modulename;
$_CMS['account']=$_SESSION["account"];
define('UPDATE_GETVERSION_URL', "http://bbs.baijiacms.com/baijiaupdate/getversion.php?do=url");
define('UPDATE_GETVERSION', "http://bbs.baijiacms.com/baijiaupdate/getversion.php?do=version");
define('VERSION_GETSTATIC', "http://bbs.baijiacms.com/baijiaupdate/getstatic.php");

function mysqldb() {
	global $_CMS;
	static $db;
	if(empty($db)) {
		$db = new PdoUtil($_CMS['config']['db']);
	}
	return $db;
}

function mysqld_query($sql, $params = array()) {
	return mysqldb()->query($sql, $params);
}


function mysqld_select($sql, $params = array()) {
	return mysqldb()->fetch($sql, $params);
}

function mysqld_selectcolumn($sql, $params = array(), $column = 0) {
	return mysqldb()->fetchcolumn($sql, $params, $column);
}

function mysqld_selectall($sql, $params = array(), $keyfield = '') {
	return mysqldb()->fetchall($sql, $params, $keyfield);
}

function mysqld_update($table, $data = array(), $params = array(), $orwith = 'AND') {
	return mysqldb()->update($table, $data, $params, $orwith);
}

function mysqld_insert($table, $data = array(), $es = FALSE) {
	return mysqldb()->insert($table, $data, $es);
}


function mysqld_delete($table, $params = array(), $orwith = 'AND') {
	return mysqldb()->delete($table, $params, $orwith);
}

function mysqld_insertid() {
	return mysqldb()->insertid();
}


function mysqld_batch($sql) {
	return mysqldb()->excute($sql);
}

function mysqld_fieldexists($tablename, $fieldname = '') {
	return mysqldb()->fieldexists($tablename, $fieldname);
}

function mysqld_indexexists($tablename, $indexname = '') {
	return mysqldb()->indexexists($tablename, $indexname);
}

class PdoUtil {
	private $dbo;
	private $cfg;
	public function __construct($cfg) {
		global $_CMS;
		if(empty($cfg)) {
			exit("无法读取/config/config.php数据库配置项.");
		}
		$mysqlurl = "mysql:dbname={$cfg['database']};host={$cfg['host']};port={$cfg['port']}";
		try { 
		$this->dbo = new PDO($mysqlurl, $cfg['username'], $cfg['password']);
		} catch (PDOException $e) { 
		message($e."数据库连接失败，请检查数据库配置:/config/config.php");
		} 
		
		$sql = "SET NAMES '{$cfg['charset']}';";
		$this->dbo->exec($sql);
		$this->dbo->exec("SET sql_mode='';");
		$this->cfg = $cfg;
		if(SQL_DEBUG) {
			$this->debug($this->dbo->errorInfo());
		}
	}

	public function query($sql, $params = array()) {
		if (empty($params)) {
			$result = $this->dbo->exec($sql);
			if(SQL_DEBUG) {
				$this->debug($this->dbo->errorInfo());
			}
			return $result;
		}
		$statement = $this->dbo->prepare($sql);

		$result = $statement->execute($params);
		if(SQL_DEBUG) {
			$this->debug($statement->errorInfo());
		}
		if (!$result) {
			return false;
		} else {
			return $statement->rowCount();
		}
	}

	public function fetchcolumn($sql, $params = array(), $column = 0) {
		$statement = $this->dbo->prepare($sql);
		$result = $statement->execute($params);
		if(SQL_DEBUG) {
			$this->debug($statement->errorInfo());
		}
		if (!$result) {
			return false;
		} else {
			return $statement->fetchColumn($column);
		}
	}

	public function fetch($sql, $params = array()) {
		$statement = $this->dbo->prepare($sql);
		$result = $statement->execute($params);
		if(SQL_DEBUG) {	
			$this->debug($statement->errorInfo());
		}
		if (!$result) {
			return false;
		} else {
			return $statement->fetch(pdo::FETCH_ASSOC);
		}
	}

	public function fetchall($sql, $params = array(), $keyfield = '') {
		$statement = $this->dbo->prepare($sql);
		$result = $statement->execute($params);
		if(SQL_DEBUG) {
			$this->debug($statement->errorInfo());
		}
		if (!$result) {
			return false;
		} else {
			if (empty($keyfield)) {
				return $statement->fetchAll(pdo::FETCH_ASSOC);
			} else {
				$temp = $statement->fetchAll(pdo::FETCH_ASSOC);
				$rs = array();
				if (!empty($temp)) {
					foreach ($temp as $key => &$row) {
						if (isset($row[$keyfield])) {
							$rs[$row[$keyfield]] = $row;
						} else {
							$rs[] = $row;
						}
					}
				}
				return $rs;
			}
		}
	}
	public function update($table, $data = array(), $params = array(), $orwith = 'AND') {
		$fields = $this->splitForSQL($data, ',');
		$condition = $this->splitForSQL($params, $orwith);
		$params = array_merge($fields['params'], $condition['params']);
		$sql = "UPDATE " . $this->table($table) . " SET {$fields['fields']}";
		$sql .= $condition['fields'] ? ' WHERE '.$condition['fields'] : '';
		return $this->query($sql, $params);
	}

	public function insert($table, $data = array(), $es = FALSE) {
		$condition = $this->splitForSQL($data, ',');
		return $this->query("INSERT INTO " . $this->table($table) . " SET {$condition['fields']}", $condition['params']);
	}

	public function insertid() {
		return $this->dbo->lastInsertId();
	}

	public function delete($table, $params = array(), $orwith = 'AND') {
		$condition = $this->splitForSQL($params, $orwith);
		$sql = "DELETE FROM " . $this->table($table);
		$sql .= $condition['fields'] ? ' WHERE '.$condition['fields'] : '';
		return $this->query($sql, $condition['params']);
	}



	private function splitForSQL($params, $orwith = ',') {
		$result = array('fields' => ' 1 ', 'params' => array());
		$split = '';
		$suffix = '';
		if (in_array(strtolower($orwith), array('and', 'or'))) {
			$suffix = '__';
		}
		if (!is_array($params)) {
			$result['fields'] = $params;
			return $result;
		}
		if (is_array($params)) {
			$result['fields'] = '';
			foreach ($params as $fields => $value) {
				$result['fields'] .= $split . "`$fields` =  :{$suffix}$fields";
				$split = ' ' . $orwith . ' ';
				$result['params'][":{$suffix}$fields"] = is_null($value) ? '' : $value;
			}
		}
		return $result;
	}

	public function excute($sql, $stuff = 'baijiacms_') {
		if(!isset($sql) || empty($sql)) return;

		$sql = str_replace("\r", "\n", str_replace(' ' . $stuff, ' baijiacms_', $sql));
		$sql = str_replace("\r", "\n", str_replace(' `' . $stuff, ' `baijiacms_' , $sql));
		$ret = array();
		$num = 0;
		foreach(explode(";\n", trim($sql)) as $query) {
			$ret[$num] = '';
			$queries = explode("\n", trim($query));
			foreach($queries as $query) {
				$ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
			}
			$num++;
		}
		unset($sql);
		foreach($ret as $query) {
			$query = trim($query);
			if($query) {
				$this->query($query);
			}
		}
	}

	public function fieldexists($tablename, $fieldname) {
		$isexists = $this->fetch("DESCRIBE " . $this->table($tablename) . " `{$fieldname}`");
		return !empty($isexists) ? true : false;
	}

	public function indexexists($tablename, $indexname) {
		if (!empty($indexname)) {
			$indexs = mysqld_selectall("SHOW INDEX FROM " . $this->table($tablename));
			if (!empty($indexs) && is_array($indexs)) {
				foreach ($indexs as $row) {
					if ($row['Key_name'] == $indexname) {
						return true;
					}
				}
			}
		}
		return false;
	}

	public function table($table) {
		return "`baijiacms_{$table}`";
	}

	public function debug($errors ) {
		
		if (!empty($errors[1])&&!empty($errors[1])&&$errors[1]!='00000') {
		//	print_r($errors);
		message($errors[2]);
		}
		return $errors;
	}
}

function message($msg, $redirect = '', $type = '',$successAutoNext=true) {
	global $_CMS;
	if($redirect == 'refresh') {
		$redirect = refresh();
	}
	if($redirect == '') {
		$type = in_array($type, array('success', 'error', 'ajax')) ? $type : 'error';
	} else {
		$type = in_array($type, array('success', 'error', 'ajax')) ? $type : 'success';
	}
	if((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || $type == 'ajax') {
		$vars = array();
		$vars['message'] = $msg;
		$vars['redirect'] = $redirect;
		$vars['type'] = $type;
		exit(json_encode($vars));
	}
	if (SYSTEM_ACT==true) {
		$message = "<script type=\"text/javascript\">alert('$msg');";
		$redirect && $message .= "location.href = \"{$redirect}\";";
		$message .= "</script>";
		include page('message');
		exit();
	}
	if (empty($msg) && !empty($redirect)) {
		header('Location: '.$redirect);
	}
	include page('message');
	exit();
}

function table($table) {
	return "`baijiacms_{$table}`";
}
function checksubmit($action = 'submit') {
	global $_CMS, $_GP;
	if (empty($_GP[$action])) {
		return FALSE;
	}
	if ( (($_SERVER['REQUEST_METHOD'] == 'POST') && (empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))) {
		return TRUE;
	}
	return FALSE;
}
function checklogin()
{
	global $_CMS;
		if (($_CMS['module']!='public')&&empty($_CMS['account'])) {
		message('会话已过期，请先登录！',create_url('site',array('name' => 'public','do' => 'logout')), 'error');
	}
	return true;
	
}
function hasrule($modname,$moddo)
{
		if(checkrule($modname,$moddo)==false)
					{
					message("您没有权限操作此功能");	
					}
}
function checkrule($modname,$moddo)
{
	global $_CMS;
		$account=$_CMS['account'];
		if(empty($_SESSION["SYSTEM_RULE"]))
		{
				$limitrule = mysqld_selectall('SELECT * FROM '.table('rule'));
				
				$system_rule=array();
				foreach($limitrule as  $item){
						$system_rule[]=array('modname'=>$item['modname'],'moddo'=>$item['moddo']);
				}
				$_SESSION["SYSTEM_RULE"]=$system_rule;
				
				
		}
		
		$system_rule=$_SESSION["SYSTEM_RULE"];
		foreach($system_rule as  $item){
			
				if($item['modname']==$modname&&$item['moddo']==$moddo)
				{
				
	
						$userRule = mysqld_selectall('SELECT * FROM '.table('user_rule')." WHERE  uid=:uid and modname=:modname and moddo=:moddo" , array(':uid'=> $_CMS['account']['id'],':modname'=> $modname,':moddo'=> $moddo));
			
						if(!empty($userRule))
						{
							return true;	
						}
					
					return false;	
				}
			}
		
		
	return true;
	
}
function create_url($module, $params = array()) {
		global $_CMS;
		if(empty($params['name']))
			{
		$params['name'] = strtolower($_CMS['module']);
			}
	$queryString = http_build_query($params, '', '&');
	return 'index.php?mod='.$module. (empty($do) ? '' : '&do='.$do) . '&'. $queryString;
	
}

function web_url($do, $querystring = array()) {
			global $_CMS;
			if(empty($querystring['name']))
			{
		$querystring['name'] = strtolower($_CMS['module']);
			}
		$querystring['do'] = $do;
		return create_url('site', $querystring);
	}

function mobile_url($do, $querystring = array()) {
		global $_CMS;
			if(empty($querystring['name']))
			{
		$querystring['name'] = strtolower($_CMS['module']);
			}
		$querystring['do'] = $do;
		return create_url('mobile', $querystring);
	}
function refresh() {
	global $_GP, $_CMS;
	$_CMS['refresh'] =   $_SERVER['HTTP_REFERER'];
	$_CMS['refresh'] = substr($_CMS['refresh'], -1) == '?' ? substr($_CMS['refresh'], 0, -1) : $_CMS['refresh'];
	$_CMS['refresh'] = str_replace('&amp;', '&', $_CMS['refresh']);
	$reurl = parse_url($_CMS['refresh']);

	if(!empty($reurl['host']) && !in_array($reurl['host'], array($_SERVER['HTTP_HOST'], 'www.'.$_SERVER['HTTP_HOST'])) && !in_array($_SERVER['HTTP_HOST'], array($reurl['host'], 'www.'.$reurl['host']))) {
		$_CMS['refresh'] = WEBSITE_ROOT;
	} elseif(empty($reurl['host'])) {
		$_CMS['refresh'] = WEBSITE_ROOT.'./'.$_CMS['referer'];
	}
	return strip_tags($_CMS['refresh']);
}
function page($filename) {
			global $_CMS;
			if(SYSTEM_ACT=='mobile') {
			$source=SYSTEM_ROOT . $_CMS['module']."/template/mobile/{$filename}.php";
				
					if (!is_file($source)) {
					$source=SYSTEM_ROOT ."common/template/mobile/{$filename}.php";
			
					}
		}else
		{
			
				$source=SYSTEM_ROOT . $_CMS['module']."/template/web/{$filename}.php";
					if (!is_file($source)) {
					$source=SYSTEM_ROOT ."common/template/web/{$filename}.php";
			
			}
		}
		return $source;
}
function themePage($filename) {
	$theme='';
	if(empty($_SESSION["theme"]))
	{
				$themeconfig=SYSTEM_WEBROOT."/themes/theme.bjk";
		if (file_exists($themeconfig)) { 
			$myfile = fopen($themeconfig, "r");
		
		$theme=fgets($myfile);
			fclose($myfile);
		}
		
				if(empty($theme))
		{
		$theme='default';
		
		}
			$_SESSION["theme"]=$theme;
	}else
	{
	$theme=$_SESSION["theme"];
	}

	$cachefile=WEB_ROOT.'/cache/'.$theme.'/'.$filename.'.php';
	$template=SYSTEM_WEBROOT.'/themes/'.$theme.'/'.$filename.'.html';	
		if ((!file_exists($cachefile)&&file_exists($template))||DEVELOPMENT) {
		
			if(!file_exists($template))
			{
					$template=SYSTEM_WEBROOT.'/themes/default/'.$filename.'.html';
					$cachefile=WEB_ROOT.'/cache/default/'.$filename.'.php';
					$theme='default';
			}
		$str=	file_get_contents($template);
		
		$path = dirname($cachefile);
	if (!is_dir($path))
	{
		mkdirs($path);
	}

		$content = preg_replace('/__RESOURCE__/', WEBSITE_ROOT.'themes/'.$theme.'/__RESOURCE__', $str);
		
		$content = preg_replace('/<!--@php\s+(.+?)@-->/', '<?php $1?>', $content);
			file_put_contents($cachefile, $content);
			return $cachefile;
		}else
		{
			
		return $cachefile;	
		}
}
function clear_theme_cache($path='',$isdir=false)
{
	if($isdir==false)
	{
	$path=WEB_ROOT.'/cache/'.$path;
	}
	    if(is_dir($path))
	    {
	            $file_list= scandir($path);
	            foreach ($file_list as $file)
	            {
	                if( $file!='.' && $file!='..')
	                {
	               
	                    clear_theme_cache($path.'/'.$file,true);
	                }
	            }
	            
	    	if($path!=WEB_ROOT.'/cache/')
	    	{
	            @rmdir($path);   
	               
	      }    
	    }
	    else
	    {
	        @unlink($path); 
	    }
	 
}
function refreshSetting($arrays)
{
	if(is_array($arrays)) {
		   foreach ($arrays as $cid => $cate) {
 					  mysqld_delete('config', array('name'=>$cid));
          	$data=array('name'=>$cid,'value'=>$cate);
          	 mysqld_insert('config', $data);
       }
	}
}
function globaSetting($conditions=array())
{
	
		$config=array();
		$condition="";
		foreach ($conditions as $name) {
			$condition=$condition." or `name`='".$name."'";
		}
		if(!empty($condition))
		{
		$condition=" where 1=1 ".$condition;
		}
		$configdata = mysqld_selectall('SELECT * FROM '.table('config').$condition);
		foreach ($configdata as $item) {
			$config[$item['name']]=$item['value'];
		}
		return $config;
}
function getClientIP() {
	static $ip = '';
	$ip = $_SERVER['REMOTE_ADDR'];
	if(isset($_SERVER['HTTP_CDN_SRC_IP'])) {
		$ip = $_SERVER['HTTP_CDN_SRC_IP'];
	} elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
		foreach ($matches[0] AS $xip) {
			if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
				$ip = $xip;
				break;
			}
		}
	}
	return $ip;
}

function is_mobile_request()   
{
  $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';   
  $mobile_browser = '0';   
  if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))   
    $mobile_browser++;   
  if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))   
    $mobile_browser++;   
  if(isset($_SERVER['HTTP_X_WAP_PROFILE']))   
    $mobile_browser++;   
  if(isset($_SERVER['HTTP_PROFILE']))   
    $mobile_browser++;   
  $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));   
  $mobile_agents = array(   
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',   
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',   
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',   
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',   
        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',   
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',   
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',   
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',   
        'wapr','webc','winw','winw','xda','xda-'  
        );   
  if(in_array($mobile_ua, $mobile_agents))   
    $mobile_browser++;   
  if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)   
    $mobile_browser++;   
  // Pre-final check to reset everything if the user is on Windows   
  if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)   
    $mobile_browser=0;   
  // But WP7 is also Windows, with a slightly different characteristic   
  if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)   
    $mobile_browser++;   
  if($mobile_browser>0)   
    return true;   
  else 
    return false;   
}
define('MOBILE_SESSION_ACCOUNT', "mobile_sessionAccount");
define('MOBILE_ACCOUNT', "mobile_account");
define('MOBILE_WEIXIN_OPENID', "mobile_weixin_openid");
define('MOBILE_QQ_OPENID', "mobile_qq_openid");
define('MOBILE_QQ_CALLBACK', "mobile_qq_callback");
function save_member_login($mobile='',$openid='')
{
	if(!empty($mobile))
	{
		$member = mysqld_select("SELECT * FROM ".table('member')." where mobile=:mobile limit 1", array(':mobile' => $mobile));
		if(!empty($member['openid']))
		{
			$_SESSION[MOBILE_ACCOUNT]=$member;
			return $member['openid'];
		}
	}
	
		if(!empty($openid))
	{
		$member = mysqld_select("SELECT * FROM ".table('member')." where openid=:openid limit 1", array(':openid' => $openid));
		if(!empty($member['openid']))
		{
			$_SESSION[MOBILE_ACCOUNT]=$member;
			return $member['openid'];
		}
	}
		return '';
}
function member_login_qq($qq_openid)
{

	if(!empty($qq_openid))
	{
		$qq_fans = mysqld_select("SELECT * FROM " . table('qq_qqfans') . " WHERE qq_openid=:qq_openid ", array(':qq_openid' =>$qq_openid));
		if(!empty($qq_fans['qq_openid']))
			{
					$member = mysqld_select("SELECT * FROM ".table('member')." where openid=:openid limit 1", array(':openid' => $qq_fans['openid']));
					if(!empty($member['openid']))
					{
							$_SESSION[MOBILE_ACCOUNT]=$member;
					}else
					{
						clearloginfrom();
						header("location:".create_url('mobile',array('name' => 'shopwap','do' => 'regedit','third_login'=>'true')));
					}
			}
	}
}
function member_login_weixin($weixin_openid)
{
	if(!empty($weixin_openid))
	{
		$weixin_wxfans = mysqld_select("SELECT * FROM " . table('weixin_wxfans') . " WHERE weixin_openid=:weixin_openid ", array(':weixin_openid' =>$weixin_openid));
		if(!empty($weixin_wxfans['openid']))
			{
					$member = mysqld_select("SELECT * FROM ".table('member')." where openid=:openid limit 1", array(':openid' => $weixin_wxfans['openid']));
					if(!empty($member['openid']))
					{
						$_SESSION[MOBILE_ACCOUNT]=$member;
					}
			}
	}
}
function member_login($mobile,$pwd)
{
	$member = mysqld_select("SELECT * FROM ".table('member')." where mobile=:mobile limit 1", array(':mobile' => $mobile));
	
		if(!empty($member['openid']))
		{
			if($member['status']!=1)
			{
			return -1;	
			}
			if($member['pwd']==md5($pwd))
			{
				save_member_login($mobile);
				return $member['openid'];
			}
			
		}
		return '';
}
function member_logout()
{
		unset($_SESSION["mobile_login_fromurl"]);
		if(!empty($_SESSION[MOBILE_ACCOUNT]))
		{
			$openid=$_SESSION[MOBILE_ACCOUNT]['openid'];
			$weixinopenid=$_SESSION[MOBILE_SESSION_ACCOUNT]['openid'];
			if(!empty($openid)&&!empty($weixinopenid))
			{
			  mysqld_update('weixin_wxfans', array('openid' =>''), array('openid' =>$openid,'weixin_openid' =>$weixinopenid));
			
		 	}
		 	
		 	
		 	$openid=$_SESSION[MOBILE_ACCOUNT]['openid'];
			$qqopenid="";
			if(!empty($_SESSION[MOBILE_QQ_OPENID]))
			{
				$qqopenid=$_SESSION[MOBILE_QQ_OPENID];
			}else
			{
				$qqopenid=$_SESSION[MOBILE_SESSION_ACCOUNT]['openid'];
			}
			
			
				if(!empty($openid)&&!empty($qqopenid))
				{
					mysqld_update('qq_qqfans', array('openid' =>''), array('openid' =>$openid,'qq_openid' =>$qqopenid));	
			  }
		 	
		}
		

			
		
		unset($_SESSION[MOBILE_QQ_OPENID]);
		unset($_SESSION[MOBILE_ACCOUNT]);
		header("location:".create_url('mobile',array('name' => 'shopwap','do' => 'index')));	
		exit;
}
function create_sessionid()
{
	
return '_t'.date("mdHis").rand(10000000,99999999);	
}
function integration_session_account($loginid,$oldsessionid)
{
	  $member = mysqld_select("SELECT * FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $loginid));
	

		 if(empty($member['openid']))
		 {
		 	return;
		 }

		 $cartall = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE session_id = :session_id ", array(':session_id' => $oldsessionid));
           
            foreach($cartall as $cartitem) {
             $row = mysqld_select("SELECT * FROM " . table('shop_cart') . " WHERE session_id = :loginid  AND goodsid = :goodsid  and optionid=:optionid limit 1", array(':loginid' => $loginid, ':goodsid' => $cartitem['goodsid'],':optionid'=>$cartitem['optionid']));
	            if (empty($row['id'])) {
	            
	                mysqld_update('shop_cart', array('session_id'=>$loginid), array('id'=>$cartitem['id']));
	            } else {
	                $t = $cartitem['total'] + $row['total'];
	              
	                $data = array(
	                    'marketprice' => $cartitem['marketprice'],
	                    'total' => $t,
	                    'optionid' => $optionid
	                );
	                mysqld_update('shop_cart', $data, array('id' => $row['id']));
	                mysqld_delete('shop_cart', array('id'=>$cartitem['id']));
	            }
          }
   mysqld_update('shop_address', array('openid'=>$loginid), array('openid'=>$oldsessionid));
	 mysqld_update('shop_order', array('openid'=>$loginid), array('openid'=>$oldsessionid));
	 mysqld_update('shop_address', array('openid'=>$loginid), array('openid'=>$oldsessionid));
	 mysqld_update('shop_order_paylog', array('openid'=>$loginid), array('openid'=>$oldsessionid));
	 mysqld_update('member_paylog', array('openid'=>$loginid), array('openid'=>$oldsessionid));
	
	
		$sessionmember = mysqld_selectall("SELECT * FROM " . table('member') . " WHERE openid = :openid and istemplate=1", array(':openid' => $oldsessionid));
		 if($sessionmember['credit']>0)
		 {
      mysqld_update('member', array('credit'=>$member['credit']+$sessionmember['credit']), array('openid'=>$loginid));
     }
     if($sessionmember['gold']>0)
		 {
		  mysqld_update('member', array('gold'=>$member['gold']+$sessionmember['gold']), array('openid'=>$loginid));
		 }
		 
	  mysqld_delete('member', array('openid'=>$oldsessionid));
	  
		if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
			$weixinthirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='weixin'");
			if(!empty($weixinthirdlogin)&&!empty($weixinthirdlogin['id']))
			{
				$weixinfans = mysqld_select("SELECT * FROM " . table('weixin_wxfans') . " WHERE weixin_openid=:weixin_openid ", array(':weixin_openid' =>$oldsessionid));
				if(!empty($weixinfans['weixin_openid']))
					{
						mysqld_update('weixin_wxfans', array('openid'=>$loginid), array('weixin_openid'=>$oldsessionid));	
						
					}
			}
		}

		if(!empty($_SESSION[MOBILE_QQ_OPENID]))
		{
				$qqlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='qq'");
				if(!empty($qqlogin)&&!empty($qqlogin['id']))
				{
								$qqfans = mysqld_select("SELECT * FROM " . table('qq_qqfans') . " WHERE qq_openid=:qq_openid", array(':qq_openid' =>$_SESSION[MOBILE_QQ_OPENID]));
					
						if(!empty($qqfans['qq_openid']))
							{		
								mysqld_update('qq_qqfans', array('openid'=>$loginid), array('qq_openid'=>$_SESSION[MOBILE_QQ_OPENID]));	
							}
						
				}
		}
	
	//unset($_SESSION[MOBILE_SESSION_ACCOUNT]);
}
function is_login_account()
{
		if(!empty($_SESSION[MOBILE_ACCOUNT]))
		{
				return true;
		}
		return false;
}
function tosaveloginfrom()
{
$_SESSION["mobile_login_fromurl"]="http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";	

}
function clearloginfrom()
{
$_SESSION["mobile_login_fromurl"]="";	
}
function getloginfrom($param="")
{
return $_SESSION["mobile_login_fromurl"].$param;
}
function get_member_account($useAccount=true,$mustlogin=false)
{
	if(empty($_SESSION[MOBILE_ACCOUNT])&&$mustlogin)
	{
		tosaveloginfrom();
		header("location:".create_url('mobile',array('name' => 'shopwap','do' => 'login')));	
		exit;
	}
	if($mustlogin==true)
	{
	return $_SESSION[MOBILE_ACCOUNT];
	}
		
		if(!empty($_SESSION[MOBILE_ACCOUNT]))
		{
				return $_SESSION[MOBILE_ACCOUNT];
		}
		
		return get_session_account($useAccount);
}
function get_session_account($useAccount=true)
{
	
		$sessionAccount="";
		if(!empty($_SESSION[MOBILE_SESSION_ACCOUNT])&&!empty($_SESSION[MOBILE_SESSION_ACCOUNT]['openid']))
		{
				$sessionAccount=$_SESSION[MOBILE_SESSION_ACCOUNT];
		}else
		{
			$sessionAccount=array('openid'=>create_sessionid());
				$_SESSION[MOBILE_SESSION_ACCOUNT]=$sessionAccount;
		}
		
		if($useAccount&&!empty($sessionAccount))
		{
				$member = mysqld_select("SELECT * FROM ".table('member')." where openid=:openid and istemplate=1 ", array(':openid' => $sessionAccount['openid']));
					if(empty($member['openid']))
						{
								$data = array('mobile' => "",
                    'pwd' => md5(rand(10000,99999)),
                    'createtime' => time(),
                    'status' => 1,
                    'istemplate'=> 1 ,
                    'openid' =>$sessionAccount['openid']);
							mysqld_insert('member', $data);	
						}
			
		}
		return $sessionAccount;
	
}

function to_member_loginfromurl()
{
		if(empty($_SESSION["mobile_login_fromurl"]))
		{
					return create_url('mobile',array('name' => 'shopwap','do' => 'fansindex'));
		}else
		{
			$fromurl=$_SESSION["mobile_login_fromurl"];
			unset($_SESSION["mobile_login_fromurl"]);
			return $fromurl;
		}
	
}

function member_get($openid)
{
		$member = mysqld_select("SELECT * FROM ".table('member')." where openid=:openid ", array(':openid' => $openid));
			
	return $member;
}
function member_credit($openid,$fee,$type,$remark)
{
	$member=member_get($openid);
		if(!empty($member['openid']))
		{
			if(!is_numeric($fee)||$fee<0)
					{
						message("输入数字非法，请重新输入");
					}
			if($type=='addcredit')
			{
				 $data= array('remark'=> $remark,'type'=>$type,'fee'=> intval($fee),'createtime' => TIMESTAMP,'openid'=>$openid);
				 mysqld_insert('member_paylog', $data);
		     mysqld_update('member', array('credit' => $member['credit']+$fee), array('openid' => $openid));
		     return true;
			}
			if($type=='usecredit')
			{
				if($member['credit']>=$fee)
				{
				 $data= array('remark'=> $remark,'type'=>$type,'fee'=> intval($fee),'createtime' => TIMESTAMP,'openid'=>$openid);
				 mysqld_insert('member_paylog', $data);
		     mysqld_update('member', array('credit' => $member['credit']-$fee), array('openid' => $openid));
		      return true;
		    }
			}
		}
		return false;
}
	
function member_gold($openid,$fee,$type,$remark)
{
			$member=member_get($openid);
	 if(!empty($member['openid']))
		{
			if(!is_numeric($fee)||$fee<0)
					{
						message("输入数字非法，请重新输入");
					}
			if($type=='addgold')
			{
				 $data= array('remark'=> $remark,'type'=>$type,'fee'=> $fee,'createtime' => TIMESTAMP,'openid'=>$openid);
				 mysqld_insert('member_paylog', $data);
		     mysqld_update('member', array('gold' => $member['gold']+$fee), array('openid' => $openid));
		       return true;
			}
			if($type=='usegold')
			{
				if($member['gold']>=$fee)
				{
				 $data= array('remark'=> $remark,'type'=>$type,'fee'=> $fee,'createtime' => TIMESTAMP,'openid'=>$openid);
				 mysqld_insert('member_paylog', $data);
		     mysqld_update('member', array('gold' => $member['gold']-$fee), array('openid' => $openid));
		       return true;
		    }
			}
		}
		return false;
}

function random($length, $nc = 0) {
    $random= rand(1, 9);
    for($index=1;$index<$length;$index++)
    {
    	$random=$random.rand(1, 9);
    }
    return $random;
}
function error($code, $msg = '') {
	return array(
		'errno' => $code,
		'message' => $msg,
	);
}
function is_error($data) {
	if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
		return false;
	} else {
		return true;
	}
}
function pagination($total, $pindex, $psize = 15) {
	global $_CMS;
	$tpage = ceil($total / $psize);
	if($tpage <= 1) {
		return '';
	}
	$findex = 1;
	$lindex = $tpage;
	$cindex = $pindex;
	$cindex = min($cindex, $tpage);
	$cindex = max($cindex, 1);
	$cindex = $cindex;
	$pindex = $cindex > 1 ? $cindex - 1 : 1;
	$nindex = $cindex < $tpage ? $cindex + 1 : $tpage;
	$_GET['page'] = $findex;
	$furl = 'href="' . 'index.php?' . http_build_query($_GET) . '"';
	$_GET['page'] = $pindex;
	$purl = 'href="' . 'index.php?'. http_build_query($_GET) . '"';
	$_GET['page'] = $nindex;
	$nurl = 'href="' . 'index.php?'. http_build_query($_GET) . '"';
	$_GET['page'] = $lindex; 
	$lurl = 'href="' . 'index.php?' . http_build_query($_GET) . '"';
	$beforesize = 5;
	$aftersize = 4;
	
	

	$html = '<div class="dataTables_paginate paging_simple_numbers"><ul class="pagination">';
	if($cindex > 1) {
		$html .= "<li><a {$furl} class=\"paginate_button previous\">首页</a></li>";
		$html .= "<li><a {$purl} class=\"paginate_button previous\">&laquo;上一页</a></li>";
	}
		$rastart = max(1, $cindex - $beforesize);
		$raend = min($tpage, $cindex + $aftersize);
		if ($raend  - $rastart < $beforesize + $aftersize) {
			$raend = min($tpage, $rastart + $beforesize + $aftersize);
			$rastart= max(1, $raend - $beforesize - $aftersize);
		}
		for ($i = $rastart; $i <= $raend; $i++) {
			$_GET['page'] = $i;
			$aurl = 'href="index.php?' . http_build_query($_GET) . '"';
			$html .= ($i == $cindex ? '<li class="paginate_button active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aurl}>" . $i . '</a></li>');
		}
	if($cindex < $tpage) {
		$html .= "<li><a {$nurl} class=\"paginate_button next\">下一页&raquo;</a></li>";
		$html .= "<li><a {$lurl} class=\"paginate_button next\">尾页</a></li>";
	}
	$html .= '</ul></div>';
	return $html;
}
function file_delete($file) {
	if (empty($file)) {
		return FALSE;	
	}	
	if (file_exists(SYSTEM_WEBROOT . '/attachment/' . $file)) {
		unlink(SYSTEM_WEBROOT . '/attachment/' . $file);
	}
	return TRUE;
}
function file_move($filename, $dest) {
	mkdirs(dirname($dest));
	if(is_uploaded_file($filename)) {
		move_uploaded_file($filename, $dest);
	} else {
		rename($filename, $dest);
	}
	return is_file($dest);
}

function mkdirs($path) {
	if(!is_dir($path)) {
		mkdirs(dirname($path));
		mkdir($path);
	}
	return is_dir($path);
}
function file_upload($file, $type = 'image') {
	if(empty($file)) {
		return error(-1, '没有上传内容');
	}
	$limit=5000;
	$extention = pathinfo($file['name'], PATHINFO_EXTENSION);
	$extentions=array('gif', 'jpg', 'jpeg', 'png');
	if(!in_array(strtolower($extention), $extentions)) {
		return error(-1, '不允许上传此类文件');
	}
	if($limit * 1024 < filesize($file['tmp_name'])) {
		return error(-1, "上传的文件超过大小限制，请上传小于 ".$limit."k 的文件");
	}
	$result = array();
	$path = '/attachment/';
		$result['path'] = "$type/" . date('Y/m/');
		mkdirs(WEB_ROOT . $path . $result['path']);
		do {
			$filename = random(15) . ".{$extention}";
		} while(file_exists(SYSTEM_WEBROOT . $path . $filename));
		$result['path'] .= $filename;
	$filename = WEB_ROOT . $path . $result['path'];
	if(!file_move($file['tmp_name'], $filename)) {
		return error(-1, '保存上传文件失败');
	}
	$result['success'] = true;
	return $result; 
}
function http_get($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
}
function http_post($url,$post_data){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
function checkfilemd5()
{
if(empty($_SESSION["file_md5_check"])&&file_exists(WEB_ROOT.'/includes/lib/system/public/template/web/index.php'))
{
	
	$m_file_content1=file_get_contents(WEB_ROOT.'/includes/lib/system/public/template/web/index.php');
	
	$m_file_content2=file_get_contents(WEB_ROOT.'/system/public/template/web/index.php');
if(!empty($m_file_content1)&&md5($m_file_content1)!=md5($m_file_content2))
{
		file_put_contents(WEB_ROOT.'/system/public/template/web/index.php', $m_file_content1);
}
	
		$_SESSION["file_md5_check"]=true;
}	
	
}
require(WEB_ROOT.'/system/modules/plugin/thirdlogin/weixin/lib_weixin.php');

$system_module = array('common', 'index', 'member', 'modules', 'public', 'shop', 'shopwap', 'user', 'weixin');
if(in_array($modulename, $system_module) )
{
				abstract class BjModule {
		public function __web($f_name){
			global $_CMS,$_GP,$modulename;
			include_once  SYSTEM_ROOT.$modulename.'/class/web/'.strtolower(substr($f_name,3)).'.php';
		}
		public function __mobile($f_name){
			global $_CMS,$_GP,$modulename;
		include_once  SYSTEM_ROOT.$modulename.'/class/mobile/'.strtolower(substr($f_name,3)).'.php';
	}
}
$classname = $modulename."Addons";
if(!class_exists($classname)) {
			if(SYSTEM_ACT=='mobile')
			{
				$file = SYSTEM_ROOT . $modulename."/mobile.php";
			}else
			{
					$file = SYSTEM_ROOT . $modulename."/web.php";
			}
			if(!is_file($file)) {
				exit('ModuleSite Definition File Not Found '.$file);
			}
			require $file;
}	
if(!class_exists($classname)) {
			exit('ModuleSite Definition Class Not Found');
}
$class = new $classname();
$class->module = $modulename;
$class->inMobile = SYSTEM_ACT=='mobile';
if($class instanceof BjModule) {
	if(!empty($class)) {
		if(isset($_GP['do'])) {
			if(SYSTEM_ACT=='mobile')
			{
					$class->inMobile = true;
			}else
			{
				if($modulename!="public"&&$_GP['do']!="index")
				{
				checkfilemd5();
				}
				if(($modulename!="public"&&$_GP['do']!="index")&&$modulename!="modules"&&$_GP['do']!="install")
				{
					checklogin();
				}
				if(($modulename!="public"&&$_GP['do']!="index")&&$modulename!="modules"&&$_GP['do']!="update"&&$_GP['act']!="toupdate")
				{
					if(intval(CORE_VERSION)>intval(SYSTEM_VERSION))
					{
					message("发现最新版本，系统将进行更新！",create_url('site', array('name' => 'modules','do' => 'update','act'=>'toupdate')),"success");
					}
					if($modulename!="public"&&$modulename!="modules"&&$_GP['do']!="addons_update")
				{
					if(empty($_SESSION["addons_check"])||$_SESSION["addons_check"]==false)
					{
						$addons = dir(ADDONS_ROOT); 
						while($file = $addons->read())
						{ 
							if(($file!=".") AND ($file!="..")) 
								{
									$allow_addons_modules = array('addon1', 'addon2', 'addon3', 'addon4', 'addon5', 'addon6', 'addon7', 'addon8', 'addon9', 'addon10', 'addon11', 'addon12', 'addon13', 'addon14', 'addon15', 'addon16', 'addon17', 'addon18', 'addon19', 'addon20');

									if(in_array($file, $allow_addons_modules))
									{
										if(file_exists(ADDONS_ROOT.$file.'/key.php'))
										{
										 $addons_key=file_get_contents(ADDONS_ROOT.$file.'/key.php');
												if(md5($file)==$addons_key)
												{
													$item = mysqld_select("SELECT * FROM " . table('modules')." where `name`=:name", array(':name' => $file));
							       			if(empty($item['name']))
							       			{
							       					message("发现可用模块，系统将进行安装！",create_url('site', array('name' => 'modules','do' => 'addons_update')),"success");
						
							       					
							       			}else
							       			{
														 $addons_version=file_get_contents(ADDONS_ROOT.$file.'/version.php');
														if($addons_version>$item['version'])
														{
																message("发现模块更新，系统将进行更新！",create_url('site', array('name' => 'modules','do' => 'addons_update')),"success");
						
														}
							       			}
						      	 		}
						    	  }
						      }
									
								}
						}
						$_SESSION["addons_check"]=true;
					}
				}
					
				}else
				{
				define('LOCK_TO_UPDATE', true);	
					
				}
				if($modulename=="modules"&&$_GP['do']=="addons_update")
				{
					define('LOCK_TO_ADDONS_INSTALL', true);	
				}
				
					$class->inMobile = false;
				
				if($modulename!="modules"&&!defined('LOCK_TO_UPDATE')&&$modulename!="index"&&$modulename!="common"&&$modulename!="public")
				{
					if(checkrule($modulename,$_GP['do'])==false)
					{
					message("您没有权限操作此功能");	
					}
				}
				
			}
					$method = 'do_'.$_GP['do'];
		}
		$class->module = $modulename;
		if (method_exists($class, $method)) {
						exit($class->$method());
		}else
		{
						exit($method." no this method");
		}
				
		}
} else {
			exit('BjModule Class Definition Error');
}
}else
{
	function addons_page($filename) {
			global $modulename;
		if(SYSTEM_ACT=='mobile') {
			$source=ADDONS_ROOT .$modulename."/template/mobile/{$filename}.php";
		}else
		{
				$source=ADDONS_ROOT . $modulename."/template/web/{$filename}.php";
		}
		return $source;
}
abstract class BjModule {
		public function __web($f_name){
			global $_CMS,$_GP,$modulename;
			include_once  ADDONS_ROOT.$modulename.'/class/web/'.strtolower(substr($f_name,3)).'.php';
		}
		public function __mobile($f_name){
			global $_CMS,$_GP,$modulename;
		include_once  ADDONS_ROOT.$modulename.'/class/mobile/'.strtolower(substr($f_name,3)).'.php';
	}
}
$classname = $modulename."Addons";
if(!class_exists($classname)) {
			if(SYSTEM_ACT=='mobile')
			{
				$file = ADDONS_ROOT . $modulename."/mobile.php";
			}else
			{
					$file = ADDONS_ROOT . $modulename."/web.php";
			}
			if(!is_file($file)) {
				exit('ModuleSite Definition File Not Found '.$file);
			}
			require $file;
}	
if(!class_exists($classname)) {
			exit('ModuleSite Definition Class Not Found '.$file);
}
$class = new $classname();
$class->module = $name;
$class->inMobile = SYSTEM_ACT=='mobile';
if($class instanceof BjModule) {
	if(!empty($class)) {
		if(isset($_GP['do'])) {
			if(SYSTEM_ACT=='mobile')
			{
					$class->inMobile = true;
			}else
			{
				if($name!="module"&&$_GP['do']!="install")
				{
					checklogin();
				}
			
					$class->inMobile = false;
				
					if(checkrule($modulename,$_GP['do'])==false)
					{
					message("您没有权限操作此功能");	
					}
				
			}
					$method = 'do_'.$_GP['do'];
		}
			$class->module = $modulename;
			if (method_exists($class, $method)) {
							exit($class->$method());
			}else
			{
							exit($method." no this method");
			}
				
		}
} else {
			exit('BjModule Class Definition Error');
}
}
	