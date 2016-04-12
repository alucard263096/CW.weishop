<?php defined('SYSTEM_IN') or exit('Access Denied');defined('LOCK_TO_UPDATE') or exit('Access Denied');
$sql = "
delete from `baijiacms_rule` where `modname`='weixin' and `moddo`='weixin';
insert into `baijiacms_rule` (`moddescription`,`modname`,`moddo`)value('微信设置','weixin','weixin');

update `baijiacms_shop_order` set paytype=1 where paytypecode='gold';
update `baijiacms_shop_order` set paytype=3 where paytypecode='delivery';
update `baijiacms_shop_order` set paytype=2 where paytypecode='weixin';
update `baijiacms_shop_order` set paytype=2 where paytypecode='alipay';
update `baijiacms_shop_order` set paytype=2 where paytypecode='bank';

CREATE TABLE IF NOT EXISTS `baijiacms_gold_order` (
  `createtime` int(10) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL,
  `openid` varchar(40) NOT NULL,
 	`ordersn` varchar(20) NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

";

mysqld_batch($sql); 