<?php
/*
api����һ����ָ��һ̨�����
�п��ܾ��Ǳ��أ��п��ܶ�̨����ÿ̨�����ṩ�Ľӿڲ�һ��
�����api-client����Ҫ��һ��ͳһ�ĵ������
������Ҫ�������ԣ�
1.���ؽӿ�:localhost
2.Զ��api�ӿڣ�api.yuan37.test,
Զ�̽ӿ�Ҳ����ָ���̨����,api.yuan37.test��Ҫ�����ѵĻ�������
client�ṩһ�׻���ȥ���ʸ��ַ���
*/
class QqApi{
	public $action='';
	
	public function __construct($action){
		$this->action=$action;
	}
	
	public function data($data){
		$action=$this->action;
		
		return $this->$action($data);
	}
	
	
	public function all($data){
		//echo $api;
		$http=H();
		$url='http://api.yuan37.test';
		if($http->postUrl($url,$data)=='200'){
			return $http->content;
		}	
	}
	
	
	
	public function charset($data){
	
		//echo $api;
		$http=H();
		$url='http://www.qq.com';
		if($http->postUrl($url,$data)=='200'){
			return $http->getCharset();
		}
		
	}
	
	public function keyword($data){
	
		//echo $api;
		$http=H();
		$url='http://www.qq.com';
		if($http->postUrl($url,$data)=='200'){
			return $http->getKeyword();
		}
		
	}	
	

	
}
