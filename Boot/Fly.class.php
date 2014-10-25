<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//Fly:boot start
class Fly{
	static public $classMap=array();
	
	static public function getClassMap($name=null){
		if(empty($name)){
			return self::$classMap;
		}else{
			if(isset(self::$classMap[$name])){
				return self::$classMap[$name];
			}else{
				return false;
			}
		}
	}
	
	static public function setClassMap($map=array()){
		if(empty($map)) return;
		return self::$classMap=array_merge(self::$classMap,$map);
	}
	
	static public function autoload($className){
		if(isset(Fly::$classMap[$className])){
			if(is_file(Fly::$classMap[$className])){
				require(Fly::$classMap[$className]);
				return;
			}
		}else{
			if(substr($className,0,3)=='Fly'&&is_file(FLY_LIB.$className.'.class.php')){
				require(FLY_LIB.$className.'.class.php');
				return;
			}
			if(substr($className,0,6)=='AppCmd'&&is_file(BIN_PATH.CMD_NAME.'.php')){
				require(BIN_PATH.CMD_NAME.'.php');
				return;
			}
			if(is_file(APP_LIB.$className.'.class.php')){
				require(APP_LIB.$className.'.class.php');
				return;
			}
			
		}
	}
	
	static public function exception($err){
		$e['message']=$err->getMessage();
		$e['file']=$err->getFile();
		$e['line']=$err->getLine();
		self::record($e,'exception');		
	}
	
	static public function error($errno, $errstr, $errfile, $errline){
		$e['message']=$errstr;
		$e['file']=$errfile;
		$e['line']=$errline;
		self::record($e,'error');	
	}
	
	static public function shutdown(){	
		$e = error_get_last();
		if($e){
			self::record($e,'shutdown');
		}
	}
	
	static public function record($e,$type=''){
		FlyLog::record($type.': '.$e['message'].' in '.$e['file'].' #'.$e['line'],'error.log');		
	}

	static private function parseArgs($argv,$argc){
		$args=array();
		$null=false;
		foreach($argv as $key=>$value){
			if(substr($value,0,1)=='-'){
				$index=substr($value,1);
				$args[$index]='*';
				$null=true;	
			}elseif(isset($index) && $null){
				$args[$index]=$value;
				$null=false;
			}
		}
		return $args;
	}	

	static public function loadExt($load,$sys=false){
		$path=$sys?FLY_LIB:APP_LIB;
		if(is_string($load)){
			$ext=$load;
			$file=$path.ucfirst($ext).'/'.$ext.'.fun.php';
			$func='load_'.$ext;
			if(is_file($file)){
				require($file);
				if(function_exists($func)){
					Fly::setClassMap($func());
				}else{
					throw new FlyException('load '.$ext.' error. function<'.$func.'>: is not in '.$file.'!');
				}
			}else{
				throw new FlyException('load '.$ext.' error. file<'.$file.'>: is not exists!');
			}
		}
		if(is_array($load)){
			foreach($load as $ext){
				$file=$path.ucfirst($ext).'/'.$ext.'.fun.php';
				$func='load_'.$ext;
				if(is_file($file)){
					require($file);
					if(function_exists($func)){
						Fly::setClassMap($func());
					}else{
						throw new FlyException('load '.$ext.' error. function<'.$func.'>: is not in '.$file.'!');
					}
				}else{
					throw new FlyException('load '.$ext.' error. file<'.$file.'>: is not exists!');
				}				
			}
		}
		return;
	}
	
	static public function load($load){
		if(is_string($load)){
			$file=$load;
			if(is_file($file)){
				$conf=require($file);
				if(is_array($conf)) C($conf);
			}
		}
		if(is_array($load)){
			foreach($load as $key => $value){
				$file=$value;
				if(is_file($file)){
					$conf=require($file);
					if(is_array($conf)){
						is_numeric($key)?C(require($file)):C($key,require($file));
					}else{
						if(is_string($key)) Fly::setClassMap($key());
					}
				}
			}
		}
		return;	
	}	

	static public function start($argv,$argc,$args=array()){
		spl_autoload_register('Fly::autoload');
		set_exception_handler('Fly::exception');
		set_error_handler('Fly::error');
		register_shutdown_function('Fly::shutdown');
		Fly::load(array(FLY_BOOT.'fly.fun.php',FLY_BOOT.'fly.conf.php'));
		Fly::loadExt(C('flyLoadMap'),true);
		C('args',Fly::parseArgs($argv,$argc));
		FlyApp::run();
	}	
}