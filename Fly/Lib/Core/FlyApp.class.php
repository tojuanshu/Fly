<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//Fly驱动文件:控制应用执行流程
class FlyApp{
	static public function run(){
		//获取class名
		if(strcasecmp(CMD_NAME,'fly')==0){
			$class=FlyApp::fly();
		}else{
			$class=strcasecmp(CMD_NAME,'flying')==0?FlyApp::flying():FlyApp::runing();		
		}
		
		
		if(!class_exists($class,true)){
			exit('class is not exists: <'.$class.'>');
		}
		

		//获取当前指向的方法
		$run=C('args.r',null,'run');
		if($run=='*'){$run='run';}
		
		//指向的方法是否有效
		$classRef=new ReflectionClass($class);
		if($classRef->hasMethod($run)){
			$methodRef=$classRef->getMethod($run);
			if($methodRef->isPublic() && !$methodRef->isStatic()){
				$classObj=new $class;

				//前置方法
				if($classRef->hasMethod('_init')){
					$initRef=$classRef->getMethod('_init');
					if($initRef->isPublic() && !$initRef->isStatic()){
						$initRef->invoke($classObj);
					}
				}
				
				//$methodRef->invoke($classObj);
				$classObj->_initialize($run);
				
				//后置方法
				if($classRef->hasMethod('_over')){
					$overRef=$classRef->getMethod('_over');
					if($overRef->isPublic() && !$overRef->isStatic()){
						$overRef->invoke($classObj);
					}
				}				
				
				
			}else{
				exit('method is ng: <'.$class.'::'.$run.'>');
			}
		}else{
			exit('method is not exists: <'.$class.'::'.$run.'>');
		}
		
		
		
	}

	
	static public function fly(){
			//设定时区
			date_default_timezone_set(C('timezone'));	
			if(C('args.c')=='app'){
				$class='FlyInstallCmd';
				return $class;
			}else{
				echo 'your input ng!';br();exit;
			}		
	}
	
		
	//flying命令
	static public function flying(){
		date_default_timezone_set(C('timezone'));//设定时区		
		$class=CMD_CLASS;
		//new $class;//实例运行
		return $class;
	}

	//bin命令
	static public function runing(){
		//加载配置及函数,类映射
		//echo is_file(APP_BOOT.'app.conf.php');exit;
		
		Fly::load(APP_BOOT.'app.conf.php');
		Fly::load(APP_BOOT.'app.fun.php');
		Fly::load(APP_CONF.strtolower(CMD_NAME).'.conf.php');
		Fly::load(APP_COMMON.strtolower(CMD_NAME).'.fun.php');
		
		//加载应用扩展
		//Fly::load(C('appLoadMap'));
		
		//print_r(C('appLoadMap'));exit;
		
		Fly::loadExt(C('appLoadMap'));
		
		//加载应用路径映射
		//Fly::setClassMap(C('appClassMap'));			
		
		date_default_timezone_set(C('timezone'));//设定时区	
		$class='AppCmd';
		//new $class;//实例运行
		return $class;
	}	
	
	

}