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

Api::yuan37('login',array('name'=>'xuyuan'));
*/

class Api{
	static public $obj=array();//ʵ�������API����
	
	static public function __callstatic($method,$datas){
		$method=ucfirst($method).'Api';		//������
		$action=$datas[0];					//������
		$data=$datas[1];					//����
		

		
		


		if(!isset(self::$obj[$method])){
			//����,��ô�������Щ�࣬�ͷ���ȥ�����Щ�࣬
			if(!Fly::getClassMap($method)){
				return 'error: '.$method.' not exists';
			}		
			self::$obj[$method]=new $method('test');
		}
		
		

		//�������
		$obj=self::$obj[$method];
		if(!method_exists($obj,$action)){
			return 'error: '.$method.' not have '.$action;
		}
		
		
		
		$result=$obj->$action($data);
		return $result;
	}
	

}
