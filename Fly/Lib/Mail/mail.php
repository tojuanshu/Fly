<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//命令执行文件，创建于2014-10-18 00:33:58
defined('FLY') || require_once(__DIR__.'/../flying.php');
class AppCmd extends FlyCmd{
/*
[root@localhost www.yuan37.com]# telnet smtp.163.com 25
Trying 220.181.12.18...
Connected to smtp.163.com.
Escape character is '^]'.
220 163.com Anti-spam GT for Coremail System (163com[20121016])

HELO smtp.163.com

250 OK

AUTH LOGIN
334 dXNlcm5hbWU6
eHV5dWFuX21lQDE2My5jb20=

334 UGFzc3dvcmQ6
eHV5dWFu
235 Authentication successful

MAIL FROM:<xuyuan_me@163.com>
250 Mail OK

RCPT TO:<1184411413@qq.com>
250 Mail OK
DATA
354 End data with <CR><LF>.<CR><LF>
HELLO XUYUAN
.
250 Mail OK queued as smtp14,EsCowECpdV1WQ0FUuRf2BQ--.1381S2 1413563633
QUIT
221 Bye
Connection closed by foreign host.
*/

	public function run(){
		fly('^_^,welcom to use <mail>');
	}
	
	
	
	
	
	
	
	public function mail(){
		exit('stop send^_^');
		Fly::loadExt('mail');
		$config=array(
			'username'=>'qycnsite@163.com',
			'password'=>'qyadmin',
			'from'=>'qycnsite@163.com'
		);

		$email=new SendEmail();
		$user=array(
			'email'			=>'1184411413@qq.com',//,893166845@qq.com
			'subject'		=>'xuyuan,你在做什么呢',
			'body'			=>'这个东西还真的蛮有趣的！'
		);
		print_r($user);
		$email->send($user);
		$email->clear();
	}	
	
	
	
	
	
	
	
}