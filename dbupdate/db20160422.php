<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.baijiacms.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 百家威信 <QQ:2752555327> <http://www.baijiacms.com>
// +----------------------------------------------------------------------

define('SYSTEM_IN', true);
require "../config/config.php";

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
		}
		return $errors;
	}
}


echo $sql = "

ALTER TABLE `baijiacms_merchant` 
ADD COLUMN `lat` VARCHAR(45) NULL DEFAULT NULL AFTER `viewcount`,
ADD COLUMN `lng` VARCHAR(45) NULL DEFAULT NULL AFTER `lat`,
ADD COLUMN `business_loc` VARCHAR(45) NULL DEFAULT NULL AFTER `lng`;

insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001001','宠儿居宠物生活馆','','18988755882','1','宝安南路沿线','鸿翔花园一栋1003铺','22.552985','114.10669','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001002','宠物时光宠物店','','18520851828','1','宝安南路沿线','松园南街','22.551191','114.106123','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001003','莎莎宠物吧','','','1','宝安南路沿线','蔡屋围五街','22.544822','114.107232','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001004','爱恬宠物医院','','','1','宝安南路沿线','罗湖区嘉宾路5048号','22.53792','114.10871','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001005','宠宝小屋','','','1','国贸','春风路3038-5号','22.53714','114.12141','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001006','盈盈猫屋','','','1','国贸','建设路1098-3号友谊大厦6座6楼A室','22.53805','114.11679','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001007','宠怡幸福宠物','','','1','东门商业圈','人民北路3131-3号','22.55581','114.12029','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001008','名犬廊','','18688752525','1','东门商业圈','乐园路与中兴路交叉口东','22.549703','114.127453','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001009','宠天下宠物屋','','','1','东门商业圈','东门老街','22.546155','114.118507','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001010','吉祥宠物','','','1','东门商业圈','解放路新起点旁','22.545262','114.117875','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001011','喵星球','','13530428782','1','东门商业圈','银都大厦/解放路1039-22号','22.54487','114.12072','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001012','犬嘟嘟宠物店','','','1','东门商业圈','深南东路银都大厦北20米/解放路1039-29','22.54487','114.12072','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001013','贵叔宠物店','','','1','东门商业圈','深南东路电信大厦西/解放路1039-23','22.54487','114.12072','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001014','老东门宠物店','','','1','东门商业圈','深南东路亚洲商业大厦北','22.5437','114.12027','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001015','红果宠物','','','1','火车站','水库新村266-1号','22.566115','114.139285','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001016','康美宠物（文锦渡店）','','','1','火车站','沿河南路文锦渡宠物城2号店','22.54068','114.12654','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001017','犬鑫犬意美容会所','','13926555680','1','火车站','桂园路桂花大厦43号-3','22.54732','114.11317','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001018','有只狗宠物工作舍 ','','','1','火车站','沿河南路与东门南路交叉口旁','22.540643','114.124034','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001019','柏豪宠物用品店','','14716468638','1','火车站','罗湖区 沿河南路怡都大厦派多格宠物(罗湖金岸汇泰大厦)','22.53441','114.12174','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001020','芒果宠物店','','','1','火车站','春风路3045-5号','22.535621','114.119905','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001021','爱宠生活馆','','18676673887','1','火车站','洪湖一街40号','22.5573','114.12278','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001022','安哥鲁','','','1','火车站','和平路1059','22.692875','113.79414','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001023','深圳动检局宠物医院 ','','','1','火车站','和平路2049号','22.54055','114.11508','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001024','博爱堂宠物医院','','13714885790','1','翠竹路沿线','罗湖区大芬地铁站A出口','22.612126','114.139403','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001025','宠之屋宠物店','','1599585684','1','翠竹路沿线','布心路辅路（华达园二期首层）','22.57374','114.13006','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001026','宝乐思宠物生活','','','1','翠竹路沿线','爱国路1046','22.5524','114.133','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001027','宠爱有家宠物店','','','1','翠竹路沿线','太宁路','22.568335','114.137819','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001028','派多格宠物（凤凰印象店）','','','1','罗湖区政府','深圳市罗湖区凤凰路181号京基凤凰印象广场F1层','22.547415','114.133481','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001029','皇家宠物医院','','','1','罗湖区政府','文锦中路8号联兴大厦北座2楼（罗湖区政府对面）','22.54906','114.1304','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001030','狗儿的尾巴','','15800222154','1','水库','罗湖区太宁路9号水库市场综合楼1044铺(喜荟城C入口对面)','22.56674','114.14146','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001031','萌喵猫舍','','15012900077','1','水库','太安路45号','22.56928','114.14132','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001032','泡泡缘宠物店','','','1','莲塘','国威路85号','22.56395','114.17419','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001033','爱丁堡宠物医院','','25703906','1','莲塘','畔山路43号','22.56545','114.17498','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001034','宠它吧','','','1','莲塘','长岭路','22.556177','114.187946','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001035','哈哈宠物乐园','','','1','莲塘','罗沙路2018号东方尊峪商铺3楼(近华润万家)','22.55784','114.191','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001036','冠能哈哈宠物乐园','','13923434567','1','莲塘','罗湖区长岭路','22.556177','114.187946','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001037','犬之梦宠物店 ','','','1','笋岗','罗湖区宝岗路165-8号(近田心村)','22.56723','114.11244','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001038','宅喵儿','','15986755601','1','笋岗','裕华路与宝岗路交口西50米','22.565707','114.112681','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001039','嘉园宠物','','','1','笋岗','笋岗东路嘉宝田花园东门12号','22.55709','114.11207','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001040','宠森纯种猫天地','','','1','文锦渡','沿河南路宠物城1号铺','22.542735','114.13553','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001041','我的挚爱犬舍','','','1','文锦渡','沿河路文锦渡宠物城21号铺','22.54068','114.12654','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001042','香喷喷宠物店  ','','','1','文锦渡','新安路文星花园17号2楼','22.5414','114.12853','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001043','宠之廊','','','1','黄贝岭','沿河南路3069','22.54348','114.1374','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001044','wingo宠物屋','','13612900915','1','黄贝岭','沿河南路3065','22.54284','114.13641','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001045','派多格宠物店','','','1','黄贝岭','沿河南路怡都大厦','22.53349','114.12228','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001046','鹏辉宠物医院 ','','','1','黄贝岭','深南东路1086号集浩大厦1楼(近黄贝岭村)','22.54655','114.13536','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001047','大派町宠物店','','','1','布心','东湖路航佳大厦A06-A07号铺','22.58196','114.13566','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001048','万牵宠物生活馆','','18665899343','1','布心','东湖路航佳大厦190-8室','22.58205','114.13564','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001049','喜多多宠物店','','13902925407','1','布心','布心路东晓路航佳大厦A15号铺','22.58196','114.13566','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001050','爱瑞宝宠物店 ','','13612972298','1','布心','东盛路1-1号','22.57496','114.13064','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001051','万牵宠物生活馆','','18665899343','1','布心','深圳市罗湖区东湖路190-8','22.581869','114.13541','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001052','爱宠居','','','1','田贝','文锦北路1083-7','22.56416','114.122695','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001053','宠宠熊宠物美容 4星','','','1','田贝','田贝二路嘉多利花园北区102商铺','22.56141','114.12531','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001054','名猫世界宠物店 3.5星','','','1','田贝','黄贝街道怡景路与爱国路交汇处湖滨新村一栋二单元C','22.56022','114.13821','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001055','派多格宠物店','','','1','田贝','广东省深圳市罗湖区田贝一路72号','22.55851','114.12516','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001056','艾可宠物生活馆','','13714885790','1','田贝','洪湖一街2-5号','22.55856','114.12377','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001057','颖宠物食品店','','','1','银湖泥岗','泥岗村','22.566782','114.098546','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001058','迷你屋宠物用品专卖店','','13826555567','1','新秀罗芳','古玩城G栋170室','22.547789','114.144768','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001059','大派町宠物店','','','1','水贝','翠竹路北路鹿鸣园1楼101-B号','22.57576','114.12828','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001060','MG猫舍','','13530232399','1','水贝','宝岗路田心村95号104','22.567875','114.114075','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001061','进口宠物用品','','13423448444','1','草铺','吓屋村87栋','22.585435','114.118105','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001062','宠立方宠物坊','','','1','','罗湖区清水河红岗路龙园山庄对面/章輋村70-2','22.58629','114.11136','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001063','乐康宠物医院','','','1','','桂园路62号','22.54841','114.11256','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001064','乐康宠物医院','','','1','','春风路4036号罗湖大厦一楼','22.53616','114.11745','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001065','糖糖宠物生活馆','','','1','','福田区深圳市罗湖区红岭中路2026号(博爱医院)','22.55162','114.10478','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001066','Pet Times宠物店','','','1','','松园西街15-2','22.551093','114.108347','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001067','贝贝动物医院','','','1','','春风路6号(高嘉花园对面)','22.542183','114.129379','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001068','千珺专业宠物美容中心(罗湖店)','','','1','','春风路向西村向华楼1层103号','22.54163','114.12612','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001069','宠物美容店','','','1','','深圳市罗湖区大望村68号103铺','22.602403','114.175611','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('001070','中心动物医院','','','1','','红桂路2100号(长城大酒店旁，天池宾馆斜对面)','22.54969','114.10628','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002001','派心思宠物生活馆','','','2','市中心区','莲花北路彩田村公交站/广东省深圳市福田区宏威路31-6','22.560905','114.058825','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002002','可可宠物店','','18680307983','2','华强北','百花六路27号长城花园15栋','22.55454','114.09055','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002003','百变宠物美容工作室','','18588400789','2','华强北','春晖苑丁单元405室','22.54176','114.06859','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002004','开心宠物吧','','13612932523','2','华强北','百花二期商业城A114','22.552063','114.094606','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002005','多格屋宠物','','','2','香蜜湖','香梅路1072号缇香名苑12号','22.54509','114.04021','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002006','乐森宠物生活馆','','','2','香蜜湖','香蜜湖路农科花卉总汇A区5号','22.54604','114.02426','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002007','乐森宠物（景田店）','','','2','香蜜湖','景田东一街15号','22.55474','114.04342','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002008','小旺福宠物美容','','','2','香蜜湖','香蜜湖路与泰然六路交叉口','22.53174','114.024416','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002009','一帆宠物医院','','18908230946','2','香蜜湖','泰然七路与泰然八路交叉口旁','22.53025','114.023854','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002010','宠来宠趣宠物店','','15099946798','2','香蜜湖','深圳市福田区福中路82','22.54501','114.06903','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002011','叮叮专业宠物美容','','15017984961','2','皇岗','皇岗海宾广场吉龙3街','22.525548','114.077949','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002012','叮叮宠物店','','','2','皇岗','福田区水围村310栋','22.522836','114.063572','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002013','尊宠宠物生活','','','2','皇岗','福民路瑞和园1楼北3001-3号商铺','22.52357','114.05571','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002014','扬帆犬舍','','13717131267','2','皇岗','皇岗新村（云顶学校）','22.518207','114.060402','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002015','诺乐宠物店','','','2','皇岗','丹桂路16号','22.5156','114.06369','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002016','丹丹宠物店','','13902349290','2','皇岗','福田南路黄御苑商城2期112铺','22.526459','114.076858','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002017','暖宠家宠物家庭寄养','','18566260225','2','皇岗','福强路1031号福民新村小区','22.52326','114.06865','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002018','宠当家宠物生活坊','','','2','皇岗','福田口岸商业广场首层95号铺（福田口岸地铁站）','22.516209','114.071146','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002019','宠物物语','','','2','皇岗','金田路1018-10号','22.52166','114.06543','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002020','犬之舍宠物店','','15817463919','2','皇岗','皇岗新村72-2号','22.522437','114.060885','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002021','奥莱特宠物美容','','13267120268','2','皇岗','皇岗公园一街云顶翠峰潮','22.51859','114.06008','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002022','犬怡宠物店','','','2','皇岗','深圳市福田区银桂路15-19','22.514667','114.060284','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002023','犬之舍宠物屋','','','2','皇岗','皇岗下围一村21-1','22.524275','114.06282','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002024','挚宠宠物店  ','','25576800','2','荔枝公园','东园路96-1，2楼（上步小学）','22.53782','114.09882','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002025','为爱犬宠物店','','15813448947','2','荔枝公园','上步东园路','22.537655','114.099366','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002026','深圳市趣致宠物保健有限公司','','','2','荔枝公园','深圳市红岭南路','22.53842','114.104404','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002027','YU.喵会所','','13751188234','2','梅林','上梅林凯丰路28号富国工业区2栋5楼（梅华小学）','22.57299','114.06422','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002028','宠宠熊宠物美容（梅林店）','','','2','梅林','梅华路碧华庭居21号商铺（碧华庭居）','22.56363','114.04931','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002029','灰太狼私人宠物会所 ','','18565796710','2','梅林','莲丰雅苑6单元','22.56154','114.05626','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002030','美宝丽都宠物用品 ','','','2','梅林','梅林路59-3号','22.56825','114.0631','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002031','瑞美宠物美容','','','2','梅林','梅林路135号梅林二村裙楼','22.56754','114.04674','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002032','苹果屋宠物中心','','','2','梅林','梅东路老茶馆旁','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002033','蔡丫头宠物工作室','','15999632971','2','梅林','莲丰大厦1栋17A1','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002034','萌宠屋','','','2','梅林','上梅林梅兴苑14栋103A（梅林街道办与新世界百货之间西横街）','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002035','时尚宠物生活馆梅林店','','','2','梅林','深圳市福田区梅林路134-17','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002036','酷狗格调','','','2','华强南','上步南路（近根据地酒吧）','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002037','牛牛宠物','','','2','华强南','福田区福田村福宁街5号','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002038','贝贝宠物 ','','','2','景田','红荔西路缇香名苑12号铺（近市政大厦，水榭花都）','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002039','派多格宠物生活馆（景田馆）','','18565625612','2','景田','景田北天健时尚新天地商铺2楼F04室（景田地铁站D出口400米）','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002040','布莱缇朵宠物店','','','2','景田','景田西路（近天然居）','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002041','狗队长宠物店','','','2','景田','深圳市福田区景田东一街15号','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002042','爱尔宝爱宠物生活馆','','','2','景田','景田北路梅富村82栋','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002043','爱派特宠物会所','','','2','景田','深圳市福田区红荔路花卉世界53号','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002044','乐塔宠物','','','2','岗厦','福华路','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002045','名将狗场','','15800031059','2','岗厦','福强路（近岗厦地铁站）','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002046','仁和宠物院','','15889729978','2','新洲','新洲北街55栋1楼','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002047','HAPPY宠物','','','2','新洲','沙嘴中心街12号','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002048','宠宠乐/爱宠乐','','13798428989','2','新洲','新洲十一街27号','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002049','美宝丽都宠物用品广场（福强店）','','','2','新洲','福强路怡和楼108-116号铺','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002050','森森宠物美容造型','','','2','新洲','新洲六街202号','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002051','崽崽宠物','','','2','新洲','沙嘴路2号','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002052','壹号苑精品宠物馆  4星','','18002540039','2','石厦','石厦北三祥韵苑首层1号铺','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002053','小宠当家','','13632519606','2','石厦','石厦北一街3-15号','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002054','宠管家（新洲店）','','','2','石厦','新洲南路丽阳天下15号铺','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002055','趴那宠物（深圳石厦店）','','83496405','2','石厦','新洲祠堂村34栋19号（吉之岛旁，中城天邑小区楼下对面）','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002056','宠物站','','','2','沙头','上沙东村24巷8号104铺（近上沙广场）','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002057','酷伴宠物','','','2','沙头','滨河路上沙东苑17号（近金海湾花园）','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002058','大笨象宠物家庭寄养','','13028846338','2','沙头','金地花园','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002059','波比宠物美容','','','2','沙头','上沙商业大街上沙小商品市场','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002060','有间宠物店','','','2','上沙下沙','下沙广场六街下沙村四坊102-103','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002061','爱狗宠物','','13798413256','2','上沙下沙','福田区沙嘴村二坊7号AB铺(福荣路美城酒店对面)','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002062','M&A有间宠物','','','2','上沙下沙','下沙村四坊10-2','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002063','家庭宠物寄养','','13798551283','2','上沙下沙','下沙八坊59号','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002064','1314宠物馆','','','2','福田保税区','深圳市福田区桂花路19-20，绒花路256号','0','0','',0);
insert into baijiacms_merchant (no,name,contact,mobile,district,business_loc,address,lat,lng,content,createtime) values ('002065','此处有家宠物店','','','2','','坂田四季花城（五和地铁A出口）','0','0','',0);



";
$db = new PdoUtil($BJCMS_CONFIG['db']);
$db->excute($sql);
echo "success";



