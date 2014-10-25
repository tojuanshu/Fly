<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
class FlyLog{
	
	//日志记录
	static public function record($log,$file=false){
		self::_log($log,$file);
	}
	

	
	
	static public function _log($info,$file=false){
		$date=date('Ymd');//时间，
		$time=date('Y-m-d H:i:s');
		$pid=getmypid();//pid
		//if(!$file) $file=$date.'.log';//文件名
		if(!$file) $file=$date.'_'.$pid.'.log';//文件名
		$fileName=LOG_PATH.$file;//文件名
		$info=print_r($info,true);//将消息字符串化
		
		$message='PID('.$pid.')=> '.$info.'|'.$time."\r\n\r\n";//记录的消息
		
		if(C('showScreen')){
			echo $message;//消息输出到屏幕
		}
		
		if(C('logRecode')){
			$file=fopen($fileName,'a+');//打开
			fwrite($file,$message);//记录
			fclose($file);//关闭
		}
	}	
	
	
	
	
	
	
	
	
}