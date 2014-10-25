<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
function load_http($load=false){
	$files=array(
		'FlyHttp'	=>__DIR__.'/FlyHttp.class.php'
	);
	if($load){
		foreach($files as $file){
			require($file);
		}
	}
	return $files;
}


function H(){
	$http=new FlyHttp();
	return $http;
}




//将域名传换为IP
function hostToIp($host){
	/*
	1.先用正则提取出精确的域名
	2.匹配不到就直接用提交过来的,不管是匹配与否，都用最终的值去配置去找
	3.找不到才自己获取
	
	
	正则的要求：匹配到的一定要是有意义的，不能匹配到其它的，匹配不到还可以直接在配置查找
	
	*/

	/*
	调试用于实时加载：
	$results=require('host.php');
	$result=$results['hosts'];
	*/
	$result=C('hosts');
	$reg='/^(?:[\w\-]+\.)?((?:[\w\-]+\.)(?:com|net|name|cn|cc|cn\.com))$/';

	if(preg_match($reg,$host,$match)){
		$host_s=$match[1];
	}else{
		$host_s=$host;
	}
	
	$ips=isset($result[$host_s])?$result[$host_s]:null;

	
	
	if($ips){
		$index=mt_rand(0,count($ips)-1);
		$ip=$ips[$index];
	}else{
		$ip=gethostbyname($host);
	}
	return $ip;
}


