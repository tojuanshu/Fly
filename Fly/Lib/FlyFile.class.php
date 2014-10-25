<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
/*
stateDir($file,$info=false)
createDir($root,$cut)
deleteDir($dir)//危险
sizeDir($dir,$change=false);
*/
class FlyFile{

//查看文件目录修改时间
static public function changeTime($path,$num=false){
		if(!file_exists($path)){//不是文件（不是路径也不是文件）
			return 'ERROR:'.$path.'不是文件或文件夹!';
		}
		
		$date=filemtime($path);
		$result=$num==true?date('Y-m-d H:i:s',$date):$date;
		return $result;
}


//求文件大小
static public function size($path,$max=104857600){//$max=100MB
		if(!file_exists($path)){//不是文件（不是路径也不是文件）
			return 'ERROR:'.$path.'不是文件或文件夹!';
		}
		$result=0;
		if(is_dir($path)){//不是路径，即是文件
			$dir=scandir($path);
			foreach($dir as $key=>$value){
				if($value!='.'&&$value!='..'){
					$paths=$path.'/'.$value;
					$result+=self::size($paths,$max);
					if($result>$max){
						return $max;
					}					
				}
			}
		}else{	
			$result+=filesize($path);
		}
		return $result;		
}




//查看文件状态
static public  function status($file,$info=false){
		$result=-1;
		$help='(-1:出错，0:不存在，1:文件，2:空文件夹，3:非空文件夹)';
		if(file_exists($file)){//是文件夹或文件		
			if(!is_dir($file)){//不为文件夹	
				$result=1;	//是一个文件	
			}else{//为文件夹
				$result=2;//设它为空文件夹
				$arr=scandir($file);//是路径时
				foreach($arr as $key=>$value){
					if($value!='.'&&$value!='..'){//有子目录
						$result=3;//非空文件夹
						break;
					}
				}				
			}					
		}else{
			$result=0;	//不是文件也不是文件夹
		}
		
		if(!$info){
			return $result;
		}else{
			return $result.$help;
		}
	}


//创建文件目录
static public function create($path,$root=''){
		//提取路径节点
		for($i=0;$i=strpos($path,'/',++$i);){$dirs[]=substr($path,0,$i);}
		//创建目录
		for($i=0;$i<count($dirs);$i++){
			if (!is_dir($root.$dirs[$i]) || !is_writable($root.$dirs[$i])) {
				if (!mkdir($root.$dirs[$i])) {//当目录不存在，或其不可写时创建一个
					return false;
				}
			}
		}
		return true;
}			
			

//删除文件目录
static public function delete($path){
		if(!file_exists($path)){//不是文件（不是路径也不是文件）
			return 'ERROR:'.$path.'不是文件或文件夹!';
		}
		if(is_dir($path)){//不是路径，即是文件
			$dir=scandir($path);
			foreach($dir as $key=>$value){//遍历文件夹下的每个内容
					if($value!='.'&&$value!='..'){//内容不是./..,注：这两个是有效路径：c:/..,c:/.
						$paths=$path.'/'.$value;//每个内容的路径形式
							self::delete($paths);
						}
				}
			rmdir($path);//foreach之后它就空了可删，递归，从里向外删，
		}else{
			unlink($path);
		}	
		return true;
}





			
}//class over