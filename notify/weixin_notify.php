<?php
define('SYSTEM_ACT', 'mobile');
$mname='modules';
$do='weixin_notify';
ob_start();
$CLASS_LOADER="driver";
require '../includes/init.php';
ob_end_flush();
exit;