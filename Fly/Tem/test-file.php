<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//命令执行文件，创建于2014-08-15 21:06:40
defined('FLY') || require_once(__DIR__.'/../flying.php');
class AppCmd extends FlyCmd{
	/*
	接收值
	取出值
	计算值
	返回值
	*/




	public function run(){
		
		$fp=fopen(PUBLIC_PATH.'text.txt','r');
		
		$arr=array();
		while(!feof($fp)){
			$arr[]=trim(fgets($fp));
		}
		
		$arr=array_filter($arr,array($this,'sss'));
		$arr=array_values($arr);
		
		$data=array();
		foreach($arr as $key => $value){
			$arr2=explode(' ',$value);
			$arr2=array_filter($arr2,array($this,'sss'));
			//array_unshift($arr2,$value);
			$arr2=array_values($arr2);
			$data[]=$arr2;
			
		}
		
		

		
		
		//关联 
		$datas=$this->match($data,
		array(
			'name'=>0,
			'age'=>1,
			'num'=>2,
			'test'=>3,
			'a4'=>array(null,'******'),
			'a5'=>array(5,date('Y-m-d H:i:s'))
		)
		);
		
		
		print_r($datas);

		
	}
	
	
	public function sss($a){
		if($a==''){
			return false;
		}else{
			return true;
		}
	}
	
	
	
	//索引转关联方法
	function match($datas,$rule){
		foreach($datas as $key => $value){
			foreach($rule as $key2 => $value2){
				if(is_array($value2)){
					if(is_null($value2[0])){
						$info[$key2]=$value2[1];
					}else{
						$info[$key2]=isset($value[$value2[0]])?$value[$value2[0]]:$value2[1];
					}
				}else{
					$info[$key2]=isset($value[$value2])?$value[$value2]:'';
				}
			}
			$data[]=$info;
		}
		
		return $data;	
	}
}