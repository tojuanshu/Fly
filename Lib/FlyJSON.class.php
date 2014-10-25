<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
class FlyJSON{

//STR(JSON)->ARR,
//ARR->STR(JSON),
public $charset='utf-8';//数据源的编码
public $sourceType;//查找的类型
public $sourcePath;//查找路径
public $sourceData;//查找数据
public $savePath=null;//保存的路径
public $sort=false;//是否对创建的JSON排序，默认不排，也就连查询行名也不要

public $name_n='name';//要查询的行的名名
public $val_n='value';//要查询的行的值名

//创建JSON
function createJSON($elem){//转入数组用于生成JSON
	if($this->sort==true) $elem=$this->arraySort($elem,$this->name_n);//数据排序
	$data=$this->_iconv($elem,$this->charset);//字符编码，URL编码
	$data2=urldecode(json_encode($data)); //JSON编码，URL方式解码
	if($this->savePath!=null){
		$data2=str_replace("},","},\r\n",$data2);
		$result=file_put_contents($this->savePath,$data2);//保存	
	}else{
		$result=$data2;
	}
	return $result;
}



//查找JSON
function selectJSON($sel=null){	
	$json=null;
	$data2=array();
	if($this->sourceType!='path'&&$this->sourceType!='data'){//数据来源类型不合法
		return '数据源必需为path或data，您指定为：'.$this->sourceType;
	}
	if($this->sourceType=='path'){//根据路径取得JSON
		$json=file_get_contents($this->sourcePath);	
	}
	if($this->sourceType=='data'){//直接取得JSON
		$json=$this->sourceData;
	}
	$data=json_decode($json,true);//把JSON转换为数组

	if($sel==null){//如果没有设定条件，全部返回
		return $data;
	}
	foreach($data as $k=>$v){//根据设定条件返回
		if(in_array($v[$this->name_n],$sel)){
			$data2[]=$data[$k];
		}
	}
	return $data2;		
}



//单点查询JSON
function callJSON($name,$value=null){
	$result=null;
	$arr=$this->selectJSON();
	foreach($arr as $key=>$val){
		if($name==$val[$this->name_n]){
			if($value!=null){//如果设定了值，则更改
				$arr[$key][$this->val_n]=$value;
				$this->createJSON($arr);
			}
			$result=$arr[$key][$this->val_n];
			return $result;
		}
	}
	return $result;
}



//==========================================================================
//根据数组子元素的值，加数组的元素
function insertJSON($ins){
	$arr=$this->selectJSON();
	foreach($ins as $k=>$v){
		$arr[]=$ins[$k];
	}
	$this->createJSON($arr);
	return $arr;
}





//根据数组子元素的值，删除数组的元素
function deleteJSON($del){
	$arr=$this->selectJSON();
	$arr2=array();
	foreach($arr as $k=>$v){
		if(in_array($v[$this->name_n],$del)){
			unset($arr[$k]);
		}
	}
	foreach($arr as $key=>$value){
		$arr2[]=$value;//重新获取以免JSON因unset后格式不符
	}
	$this->createJSON($arr);
	return $arr;
}


//根据数组子元素的值，改数组的元素
function updateJSON($upd){
	$arr=$this->selectJSON();
	foreach($upd as $k=>$v){
			foreach($arr as $key=>$value){
				if($v[$this->name_n]==$value[$this->name_n]){
					$arr[$key]=$upd[$k];
				}
			}
	}
	$this->createJSON($arr);
	return $arr;
}









//=========调用工具函数===============================================================



	//数组排序
	function arraySort($arr,$keys,$type='asc'){//要排的二维数组，要排的元素，逆序还是顺序
		$keysvalue = $new_array = array();//新建两个数组，存放排序元素的顺序，用来返回最后结果数组
		foreach ($arr as $k=>$v){
			$keysvalue[$k] = $v[$keys];//取得要排序的那个元素
		}
		if($type == 'asc'){//对排序元素排序
			asort($keysvalue);
		}else{
			arsort($keysvalue);
		}
		reset($keysvalue);//foreach后重置指针位置
		foreach ($keysvalue as $k=>$v){
			$new_array[] = $arr[$k];//根据排序元素的顺序，重新旧数组的顺序
		}
		return $new_array;//返回排好序的数组
	}








//URL方式编码
function _iconv($elem,$charset){
  if(is_array($elem)){
	$arr=array();
    foreach($elem as $k=>$v){
      $arr[$this->_iconv($k,$charset)] = $this->_iconv($v,$charset);
    }
    return $arr;
  }
  return urlencode(iconv($charset,"UTF-8//IGNORE",$elem));
}







}//class over





