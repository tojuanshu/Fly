<?php
/*
api并不一定是指向一台服务的
有可能就是本地，有可能多台服务，每台服务提供的接口不一样
这里的api-client就是要做一个统一的调用入口
这里我要这样测试：
1.本地接口:localhost
2.远程api接口：api.yuan37.test,
远程接口也可是指向多台服务,api.yuan37.test主要放自已的机密数据
client提供一套机制去访问各种服务

Api::yuan37('login',array('name'=>'xuyuan'));
*/

class Api{
	static public $obj=array();//实例化后的API对象
	
	static public function __callstatic($method,$datas){
		$method=ucfirst($method).'Api';		//对象名
		$action=$datas[0];					//方法名
		$data=$datas[1];					//参数
		

		
		


		if(!isset(self::$obj[$method])){
			//类检测,怎么导入的这些类，就反向去检测这些类，
			if(!Fly::getClassMap($method)){
				return 'error: '.$method.' not exists';
			}		
			self::$obj[$method]=new $method('test');
		}
		
		

		//方法检测
		$obj=self::$obj[$method];
		if(!method_exists($obj,$action)){
			return 'error: '.$method.' not have '.$action;
		}
		
		
		
		$result=$obj->$action($data);
		return $result;
	}
	

}
