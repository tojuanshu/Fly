<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//Fly:lead file
substr(PHP_SAPI,0,3)=='cli' || exit('fly only run for cli!');
defined('FLY') || define('FLY',str_replace('\\','/',rtrim(dirname(__DIR__),'/\\').'/'));
define('IS_WIN',strtoupper(substr(PHP_OS,0,3)=='WIN'));
define('OUTPUT_GBK',	IS_WIN?TRUE:FALSE);
define('ROOT_PATH',		FLY);
define('LOG_PATH',		ROOT_PATH.'Log/');
define('BIN_PATH',		ROOT_PATH.'bin/');
define('CONF_PATH',		ROOT_PATH.'Conf/');
define('PUBLIC_PATH',	ROOT_PATH.'Public/');
define('FLY_PATH',		ROOT_PATH.'Fly/');
define('FLY_BOOT',		FLY_PATH.'Boot/');
define('FLY_LIB',		FLY_PATH.'Lib/');
define('FLY_TPL',		FLY_PATH.'Tpl/');
define('APP_PATH',		ROOT_PATH.'App/');
define('APP_BOOT',		ROOT_PATH.'Boot/');
define('APP_DB',		APP_PATH.'Db/');
define('APP_LIB',		APP_PATH.'Lib/');
define('APP_CONF',		APP_PATH.'Conf/');
define('APP_COMMON',	APP_PATH.'Common/');
define('CMD_NAME',		substr(basename($argv[0]),0,-4));
define('CMD_CLASS',		ucfirst(CMD_NAME).'Cmd');
define('CMD_PATH',		BIN_PATH.CMD_NAME.'.php');
require(FLY_BOOT.'/Fly.class.php');
Fly::start($argv,$argc);exit;