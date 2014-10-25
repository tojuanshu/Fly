<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
/*
使用方法：
$db=D();
$db->table()->where()->group()->order()->limit()->select();

功能包的引导文件：
1.把需要调用的文件进行注册,实例的时候去找
*/

//把文件注册到系统
function load_db($load=false){
	$files=array(
	'FlyDB'			=>__DIR__.'/FlyDB.class.php',//中寄文件
	'FlyMysql'		=>__DIR__.'/FlyMysql.class.php',//mysql驱动
	'FlyMysqli'		=>__DIR__.'/FlyMysqli.class.php',//mysqli驱动
	'FlyPdo'		=>__DIR__.'/FlyPdo.class.php'//pdo驱动	
	);
	if($load){
		foreach($files as $file){
			require($file);
		}
	}
	return $files;
}



//调用方法
function D($dbSet='DB_SET'){
	static $db=array();	
	if(is_array($dbSet)){//直接传递参数，不进行缓存
		$pdo=new FlyDB($dbSet);
		return $pdo;
	}else{
		if(!isset($db[$dbSet])){
			$pdo=new FlyDB(C($dbSet));	
			$db[$dbSet]=$pdo;			
		} 
		return $db[$dbSet];
	}
}

//调用方法
function DD($dbSet='DB_SET'){
	$db=array();	
	if(is_array($dbSet)){//直接传递参数，不进行缓存
		$pdo=new FlyDB($dbSet);
		return $pdo;
	}else{
		if(!isset($db[$dbSet])){
			$pdo=new FlyDB(C($dbSet));	
			$db[$dbSet]=$pdo;			
		} 
		return $db[$dbSet];
	}
}





