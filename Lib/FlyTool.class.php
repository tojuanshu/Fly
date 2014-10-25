<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
/*
功能要求：
1.常用字符串，正则处理
2.时间处理，
3.文件处理，
3.程序提示跳转处理
Filter($data,array('html','space','sql'));
spaceFilter($data)--------过滤掉空白
tagsFilter($data)--------html过滤
htmlFilter($data)--------html转义标签
unhtmlFilter($data)------转义标签还原为html
sqlFilter($data)---------sql过滤--引号（',",\）
strCut($str,$length,$end='...')---字符串截取

alertURL($type,$url,$info)
alertClose($info)


*/

class FlyTool{
	//返回类的反射信息	
	public function classInfo($class){return Reflection::export(new ReflectionClass($class));}


//====字符串处理===========================================================
//url,html,charset,space,sql,strip_tags


	//过滤html,sql,space
	static public function PluckFilter($data,$type=1){
		if(is_array($data)){
			$result=array();//$data可能是个空数组
			foreach ($data as $key=>$value){
				$result[$key] = self::PluckFilter($value,$type);  //递归
			}
		}else if(is_object($data)){
			foreach ($data as $key=>$value){
				$result->$key = self::PluckFilter($value,$type);  //递归
			}
		}else {
			$result=$data;
			switch($type){
				case 3:$result=get_magic_quotes_gpc()?$result:addslashes($result);
				case 2:$result=strip_tags($result);
				case 1:$result=trim($result);
			}
		}
		return $result;
	}


	//编码转换
	static public function iconvFilter($from,$to,$data){
		if(is_array($data)){
			$result=array();
			foreach($data as $key=>$value){
				$result[self::iconvFilter($from,$to,$key)]=self::iconvFilter($from,$to,$value);
			}
		}else if(is_object($data)){
			foreach($data as $key=>$value){
				$key=self::iconvFilter($from,$to,$key);
				$result->$key=self::iconvFilter($from,$to,$value);
			}
		}else{
			$result=iconv($from,$to,$data);
		}
		return $result;
	}	
	
	

	//URL方式编码,支持编码转换 
	static public function urlFilter($data,$from=null,$to=null){
		if(is_array($data)){
			$result=array();
			foreach($data as $key=>$value){
				$result[self::urlFilter($key,$from,$to)]=self::urlFilter($value,$from,$to);
			}
		}else if(is_object($data)){
			foreach($data as $key=>$value){
				$key=self::urlFilter($key,$from,$to);
				$result->$key=self::urlFilter($value,$from,$to);
			}
		}else{
			if($from==null||$to==null){
			$result=urlencode($data);
			}else{
				$result=urlencode(iconv($from,$to,$data));
			}
		}
		return $result;
	}


	//过滤标签
	static public function tagsFilter($data){
		if(is_array($data)){
			$result=array();
			foreach ($data as $key=>$value){
				$result[$key] = self::tagsFilter($value);  //递归
			}
		}else if(is_object($data)){
			foreach ($data as $key=>$value){
				$result->$key = self::tagsFilter($value);  //递归
			}
		}else {
			$result=strip_tags($data);
		}
		return $result;		
	
	}

	
	
	//过滤空白
	static public function spaceFilter($data){
		if(is_array($data)){
			$result=array();
			foreach ($data as $key=>$value){
				$result[$key] = self::spaceFilter($value);  //递归
			}
		}else if(is_object($data)){
			foreach ($data as $key=>$value){
				$result->$key = self::spaceFilter($value);  //递归
			}
		}else {
			$result = trim($data);
		}
		return $result;		
	
	}


	//html转意标签
	static public function htmlFilter($data){
		if(is_array($data)){
			$result=array();//$data可能是个空数组
			foreach ($data as $key=>$value){
				$result[$key] = self::htmlFilter($value);  //递归
			}
		}else if(is_object($data)){
			foreach ($data as $key=>$value){
				$result->$key = self::htmlFilter($value);  //递归
			}
		}else {
			$result=htmlspecialchars($data);
		}
		return $result;
	}
	
	
	
	
//-------------------------------------------------------------------------------
	//转义标签还原为html	
	static public function unhtmlFilter($data){
		if(is_array($data)){
			$result=array();
			foreach ($data as $key=>$value){
				$result[$key] = self::unhtmlFilter($value);  //递归
			}
		}else if(is_object($data)){
			foreach ($data as $key=>$value){
				$result->$key = self::unhtmlFilter($value);  //递归
			}
		}else{
			$result=htmlspecialchars_decode($data);
		}
		return $result;
	}	
//-----------------------------------------------------------------------------------
	//sql转义引号（',",\）
	static public function sqlFilter($data){			
		if(get_magic_quotes_gpc()){return $data;}
		if(is_array($data)){
			$result=array();
			foreach ($data as $key=>$value){
				$result[$key] =self::sqlFilter($value);  //递归
			}
		}else if(is_object($data)){
			foreach ($data as $key=>$value){
				$result->$key =self::sqlFilter($value);  //递归
			}
		}else{	
			$result=addslashes($data);
			}
		return $result;		
	}	

	
	//字符串截取
	static public function strCut($str,$length,$end='...'){
		if(strlen($str)>$length){
			$str=substr($str,0,$length);
			$str.=$end;
		}
		return $str;			
	}
	

//======正则处理====================================================================		







//=====程序提示跳转处理=============================================================	
	//弹窗
	static public function alert($info) {
		echo "<script type='text/javascript'>alert('$info');</script>";
		exit();
	}		

	//弹窗跳转
	static public function alertURL($type,$url,$info) {
		if($url=='back'){
			switch($type){
				case 'empty'  :	echo "<script type='text/javascript'>history.back();</script>";break;
				case 'alert'  :	echo "<script type='text/javascript'>alert('$info');history.back();</script>";break;
				case 'confirm':	echo "<script type='text/javascript'>if(confirm('$info')){history.back();}</script>";break;
				default:echo "<script type='text/javascript'>alert('参数：empty,alert,confirm')</script>";
			}			
		}else{
			switch($type){
				case 'empty'  :	echo "<script type='text/javascript'>location.assign('$url');;</script>";break;
				case 'alert'  :	echo "<script type='text/javascript'>alert('$info');location.assign('$url');;</script>";break;
				case 'confirm':	echo "<script type='text/javascript'>if(confirm('$info')){location.assign('$url');}</script>";break;
				default:echo "<script type='text/javascript'>alert('参数：empty,alert,confirm')</script>";
			}
		}
		exit();
	}

	
	
	//弹窗关闭
	static public function alertClose($info) {
		echo "<script type='text/javascript'>alert('$info');window.close();</script>";
		exit();
	}

		

			
//====日期日间处理==============================================================================			
	//取得日期时间
	static public function dateTimes($_i=0,$_u='-',$_s=' ',$_c=':'){
		$dateTime=date('Y'.$_u.'m'.$_u.'d'.$_s.'H'.$_c.'i'.$_c.'s',time()+$_i);
		return $dateTime;
	}

	//取得日期
	static public function dates($_i=0,$_u='-'){
		$date=date('Y'.$_u.'m'.$_u.'d',time()+$_i);
		return $date;
	}

	//取得时间
	static public function times($_i=0,$_c=':'){
		$time=date('H'.$_c.'i'.$_c.'s',time()+$_i);
		return $time;
	}

	//取得毫秒数
	static public function millis($_d=3){
		list($msec, $sec) = explode(' ', microtime());//得到精确的时间戳
		$msec=$msec*pow(10,$_d);
		$msec=ceil($msec);
		if($msec<10){
			$msec='00'.$msec;
		}
		if($msec>=10&&$msec<100){
			$msec='0'.$msec;
		}
		return $msec;
	}
	
	//取得日期时间毫秒数
	static public function dateTimeMillis($_i=0,$_u='-',$_s=' ',$_c=':',$_f=':',$_d=3){
		$dateTimeMilli=self::dateTimes($_i,$_u,$_s,$_c).$_f.self::millis($_d);
		return $dateTimeMilli;
	}
			
//=====二维数组排序==============================================================
	static function array_sort($arr,$keys,$type='asc'){//要排的二维数组，要排的元素，逆序还是顺序
		$keysvalue = $new_array = array();//新建两个数组，存放排序元素的顺序，用来返回最后结果数组
		foreach ($arr as $k=>$v){
			$keysvalue[$k] = $v[$keys];//取得要排序的那个元素
		}
		if($type == 'desc'){//对排序元素排序,跟据值排序，键跟着变
			arsort($keysvalue);
		}else{
			asort($keysvalue);
		}
		reset($keysvalue);//foreach后重置指针位置
		foreach ($keysvalue as $k=>$v){
			$new_array[] = $arr[$k];//根据排序元素的顺序，重新旧数组的顺序
		}
		//sort($new_array);
		return $new_array;//返回排好序的数组
	}			




	
}//class over



