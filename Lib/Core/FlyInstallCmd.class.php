<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//Fly系统安装程序
class FlyInstallCmd extends FlyCmd{
	public function version(){
		echo C('version');
	}
	
	public function run(){
		echo ">>>>>>>>>You will create an application for Fly!<<<<<<<<<";br();
		echo '-------------------------------------------------------';br();
		fly();
		echo '-------------------------------------------------------';br();
		br();br();
		//are you ready?
		
		$create="Check the environment befor create it!check now?(y/n)";
		$answer=FlyCli::option($create,array('y','n'),'n');
		if($answer!='y'){
			exit('exit fly-app create!'."\r\n");
		}
		//1.检查Fly所在目录的操作权限
		echo 'check(authority): ';
		if(!is_readable(ROOT_PATH) || !is_writable(ROOT_PATH)){
			exit("<Fly> not authority-----ng\r\n");
		}else{
			echo "read and write-----ok\r\n";
		}
		
		
		
		//2.如果Fly在根目录下，提示异常，
		echo 'check(rootpath): ';
		if(strchr(ROOT_PATH,'/')=='/'){
			exit("<Fly> can't in rootpath-----ng\r\n");
		}else{
			echo ROOT_PATH."-----ok\r\n";
		}
		
		//3.如果有Fly以外的文件，提示异常,	
		echo 'check(only Fly): ';
		$dir=scandir(ROOT_PATH);
		foreach($dir as $key=>$value){
			if($value!='.'&&$value!='..'&&$value!='Fly'){
				exit("<Fly> find ".ROOT_PATH.$value."-----ng\r\n");
			}
		}
		echo "no't have other file-----ok\r\n";
		

		echo "Check it ok!";
		br();br();br();
		echo 'you will create <'.FLY.'*>,are you ok?(y/n)';
		if(trim(fgets(STDIN))=='y'){
			$this->createApp();
		}else{
			exit("no do it\r\n");	
		}	
		
		
		br();br();br();
		FlyCli::stop('how create a simple cmd?please read it...(enter key go on)');
		FlyCli::stop("1.execute: php ".ROOT_PATH.'flying.php -c cmd');
		FlyCli::stop("2.input: <cmdname>");	
		FlyCli::stop("3.find: ".BIN_PATH.'<cmdname>.php');
		echo "create success!thank you for your use.";br();br();br();
	
	
		exit;

	}
	
	
	
	
	
	
	public function createApp(){
			//创建目录
			$paths=array('bin/','Boot/','Log/','Public/','App/Lib/','App/Db/','App/Conf/','App/Common/');
			foreach($paths as $path){
				if(FlyFile::create($path,ROOT_PATH)) echo 'create: '.ROOT_PATH.$path;br();
			}

			//写入配制
			file_put_contents(APP_BOOT.'app.conf.php',file_get_contents(FLY_TPL.'app.conf.php'));
			echo 'create: '.APP_BOOT.'app.conf.php';br();
			
			//写入函数
			file_put_contents(APP_BOOT.'app.fun.php',file_get_contents(FLY_TPL.'app.fun.php'));
			echo 'create: '.APP_BOOT.'app.fun.php';br();
			
			file_put_contents(FLY.'flying.php',file_get_contents(FLY.'Fly/Tpl/flying.php'));
			echo 'create: '.FLY.'flying.php';br();
			
			echo "app create ok!\r\n";
		//命令创建
	

	}
}