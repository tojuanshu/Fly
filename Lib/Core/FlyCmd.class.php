<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//Fly命令文件：促使命令执行
class FlyCmd{
	public $_help=array();
	
	//实例化时自动执行
	public function __construct(){
		
	}




	//实例化过程中执行
	public function _initialize($run){

		if(C('args.s')){$this->_shell();exit();}//生成shell脚本
		if(C('args.h')){$this->_help();exit();}//显示help信息	
		if(C('args.v')){fly();exit();}//显示版本

	
	
		//执行方法	
		$while=C('args.w',null,'');//执行周期	

	
		
		if(preg_match('@^(\d+)/(\d+)$@',$while,$types)){
			$type1=intval($types[1]);
			$type2=intval($types[2]);
			$start=microtime(true);	
			while(true){
				$end=microtime(true);
				if($end-$start>$type1){
					break;
				}
				$this->$run();
				usleep($type2);
			}			
		}else{
			$this->$run();
		}
	}







	
	//示显帮助信息,系统信息合并用户信息，只是显示合并了，用户还是可用，但不建议有单字母
	private function _help(){
		$this->_help=array(
		'-h'=>':show help!',
		'-w'=>':\d+/\d+ whiles',
		'-r'=>':method (run)'
		);
		if(isset($this->help)) $this->_help=array_merge($this->_help,$this->help);
		print_r($this->_help);
	}
	
	
	//windows下生成.bat文件可快捷访问
	private function _shell(){
		if(!IS_WIN){echo 'NG:only by WIN!';br();exit();}
		$args=C('args');

		
		$arr=explode(':',CMD_PATH);
		$root=$arr[0].':';
		$path=dirname($arr[1]);
		
		$bin=CMD_PATH;
		unset($args['s']);
		foreach($args as $key => $value){
			if($value=='*'){
				$bin.=' -'.$key;
			}else{
				$bin.=' -'.$key.' '.$value;
			}
		}

		$cmd="@echo off\r\n";
		$cmd.=$root."\r\n";
		$cmd.='cd '.$path."\r\n";
		$cmd.='php '.$bin."\r\n";
		$cmd.='cmd.exe /k';

		if(file_put_contents(BIN_PATH.CMD_NAME.'.bat',$cmd)){
			echo 'create ok:'.BIN_PATH.CMD_NAME.'.bat';
		}
		
		
	}
	
	
}