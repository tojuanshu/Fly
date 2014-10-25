<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//Fly系统命令
class FlyingCmd extends FlyCmd{
	public function version(){
		echo C('version');
	}
	
	public function run(){
		if(C('args.c')=='cmd'&&is_dir(BIN_PATH)){
			echo("please input cmd name:");
			$cmd=trim(fgets(STDIN));
			
			if(!preg_match('/^[a-z_](\w){0,9}$/i',$cmd)){
				exit("your input ng!\r\n");
			}
			
			if(strtolower(substr($cmd,0,3))=='fly'){
				exit("<cmdname> can't start 'fly'!");
			}
			
			$binFile=BIN_PATH.$cmd.'.php';
			
			if(is_file($binFile)){
				exit("NG: {<{$cmd}.php> is exists");
			}
			
			$binContent=file_get_contents(FLY_TPL.'bin.php');
			$binContent=str_replace(array('<parentClass>','<defaultAction>','<welcome>','<createTime>'),
			array('FlyCmd','run','^_^,welcom to use <'.$cmd.'>',date('Y-m-d H:i:s')),$binContent);
			
			if(file_put_contents($binFile,$binContent)){
				echo 'cmd: <'.$cmd.'> create ok!';br();
			}
			
		}else{
			exit("your input ng!\r\n");
		}
	}
}