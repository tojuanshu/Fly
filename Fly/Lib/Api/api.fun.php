<?php

//把文件注册到系统
function load_api($load=false){
	$files=array(
	'Api'			=>__DIR__.'/Api.class.php',
	'QqApi'			=>__DIR__.'/QqApi.class.php',
	'Yuan37Api'		=>__DIR__.'/Yuan37Api.class.php',
	'ToolApi'		=>__DIR__.'/ToolApi.class.php',
	'HproseApi'		=>__DIR__.'/HproseApi.class.php'
	);
	if($load){
		foreach($files as $file){
			require($file);
		}
	}
	return $files;
}








