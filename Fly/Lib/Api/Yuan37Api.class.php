<?php
class Yuan37Api{
	public $action='';
	public $data=array();
	public $result='';
	
	
	//ͳһ����Api::server(module)->ask(data);
	
	
	//Ҫ������ģ��,�ڴ˽���Ҫ�����ģ��
	public function __construct($action){
		$this->action=$action;
	}
	
	
	//����,���,��Щ��ͬ�����ڴ˴���,���ڴ˽��ղ���
	public function ask($data){
		$http=H();
		$url='http://api.yuan37.test';
		if($http->postUrl($url,$data)=='200'){
			return $http->content;
		}	
	}
	
	
	
	//��¼
	public function login($data){
		$data['api']='login';
		return $this->ask($data);
	}
	
	
	//�˳�
	public function logout($data){
		$data['api']='logout';
		return $this->ask($data);
	}	
	

	
}
