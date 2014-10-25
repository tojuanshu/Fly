<?php
class ToolApi{
	public $action='';
	public $data=array();
	public $result='';
	
	
	//统一处理：Api::server(module)->ask(data);
	
	
	//要操作的模块,在此接收要处理的模块
	public function __construct($action){
		$this->action=$action;
	}
	
	
	public function test($data){
		$url=FlyCli::ask('pleas input url: ');
		$http=H();
		if($http->getUrl($url,$data)=='200'){
			return $http->getKeyword();
		}else{
			return $http->getDebug();
		}
	}
	
	
	
	public function charset($data){
	
		//echo $api;
		$http=H();
		$url='http://php.net';
		if($http->postUrl($url,$data)=='200'){
			return $http->getCharset();
		}
		
	}
	
	public function keyword($data){
	
		//echo $api;
		$http=H();
		$url='http://php.net';
		if($http->postUrl($url,$data)=='200'){
			return $http->getKeyword();
		}
		//echo $http->getDebug();
		
	}
	
}
