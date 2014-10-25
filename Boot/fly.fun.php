<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//Fly:boot funciton
function C($name=null,$value=null,$default=null){
    static $_config = array(); 
    if(empty($name)){return $_config;}
    if(is_string($name)){
		$name=explode('.', $name);	
        if(count($name)==1){
            if(is_null($value)){
                $value=isset($_config[$name[0]]) ? $_config[$name[0]] : $default;
			}else{
				$_config[$name[0]] = $value;
			}
            return $value;
        }
        if(count($name)==2){
			if(is_null($value)){
				$value=isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
			}else{
				$_config[$name[0]][$name[1]] = $value;
			}
			return $value;
		}
        if(count($name)==3){
			if(is_null($value)){
				$value=isset($_config[$name[0]][$name[1]][$name[2]]) ? $_config[$name[0]][$name[1]][$name[2]] : $default;
			}else{
				$_config[$name[0]][$name[1]][$name[2]] = $value;
			}
			return $value;
		}		
    }
    if(is_array($name)){	
        $_config = array_merge($_config,$name);
        return $name;
    }
    return null;
}

function fly($info='',$return=false){
	$logo=file_get_contents(FLY_TPL.'logo.fly');
	if(empty($info)) $info=C('copyright');
	$v=C('version');
	$w=str_pad($info,28,' ',STR_PAD_BOTH);
	$logo=str_replace(array('0','1','i','l','v','w'),array(' ','_','/',"\r\n",$v,$w),$logo);
	$logo="\r\n".$logo."\r\n";
	if($return){return $logo;}else{echo $logo;}
}

function br(){
	echo "\r\n";
}

function echos($data='',$return=false){
	if(OUTPUT_GBK) $data=FlyTool::iconvFilter('utf-8','gbk//IGNORE',$data);
	if($return){
		return $data;
	}else{
		print_r($data);
	}
}

function echogbk($data='',$return=false){
	return echos($data,$return);
}

