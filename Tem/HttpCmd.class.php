<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//命令执行文件
class HttpCmd extends FlyCmd{
	public $help=array(
		'-u'=>'this is dog',
	);
	
	
	public function init(){
		//$this->whiles=true;
	}

	public function test(){
		echo date('Y-m-d H:i:s');br();
	}

	public function img(){
		$http=new FlyHttp();
		$http->debug=true;
		$url='http://www.zuzuche.com/';
		$code=$http->getUrl($url);
		if($code=='200'){
			$imgs=FlyPluck::img($http->content);
			foreach($imgs as $img){
				if($http->getUrl($img)=='200'){
					file_put_contents(PUBLIC_PATH.basename($img),$http->content);
					echo 'ok:'.$img;br();	
				}
			}	
		}
	}
	
	public function run(){	
		//$url='http://www.sina.cn';
		//$url='http://www.baidu.com.cn';
		//$url='www.google.com.hk';
		//$url='http://www.qycn.com';
		$url='www.zuzuche.com';
		//echo C('args.t');
		$this->go($url);
	}
	
	
	//采集
	public function go($url){
		$http=new FlyHttp();
		$http->debug=true;
		$http->ctimeout=3;
		$code=$http->getUrl($url);
		switch($code){
			case '200':$this->code200($http);break;
			case '301':
			case '302':$this->code302($http);break;
			case '999':$this->code999($http);break;
			default:   $this->codeOther($http);
		}
		
	}
	

	
	//200处理
	public function code200($http){
			$charset=$http->getCharset();
			if($charset!='gbk'&&$charset!='unknow'){
				echo FlyTool::iconvFilter($charset,'gbk//IGNORE',$http->getKeyword());br();
			}else{
				echo $http->getKeyword();
			}	
	}
	
	
	//302处理
	public function code302($http){
		echo $http->code.':'.$http->location;br();
		$this->go($http->location);
	}
	
	//999处理
	public function code999($http){
		print_r($http->getDebug());
		echo $http->code;
	}

	//othecode处理
	public function codeOther($http){
		echo $http->code;
	}
	
}