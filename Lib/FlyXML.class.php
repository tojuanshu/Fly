<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
/*
2013-09-29 16:48:48 作了值的对象默认值设定优化


XML生成类
使用方法：
$xml=new FlyXML();//新建一个XML文档
$children[0]=$xml->setDOM($v);//传一个数组用以创建DOM标签（如下方数组）
$parent=$xml->appendChild($n);//把创建的标检插入父元素
$xml->asXMLW($parent);//把哪个元素放入DOM中后写入

$v=Array(
'name'=>'children',
'value'=>array('type'=>1,'val'=>'aaaa'),
'attr'=>array('name'=>'alerts','class'=>'org.dbaudit.screen.monitor.AlertsScreen')
)


$n=Array(
'name'=>'parent',
'value'=>array('type'=>2,'val'=>$children),
'attr'=>array('name'=>'alerts','class'=>'org.dbaudit.screen.monitor.AlertsScreen')
)
*/

class FlyXML{


	//------创建时需用到-------------------------------
	private $dom;//生成xml时，创建的DOM文档
	private $element;//setDOM生成的元素
	private $arrayNum=0;//数组来源时，数组的指针
	private $sort=false;//是否对创建的XML排序，默认不排，也就连查询行名也不要

	//------增删改查时需用到----------------------------------------
	private $xml;//要查询的XML文档
	private $row;//要查询的XML文档中的哪些行

	//---需要设定的参数--------------------------------
	private $xmlType='attr';//生成val/attr的XML可自由设定,生成，读取都是这个设定
	private $savePath=null;//要保存到的路径
	private $sourceType='path';//数据源类型
	private $sourceData;//数据源为XML
	private $sourcePath;//数据源为XML路径
	private $rootAttr=array();//根结点的属性
	private $rootName='root';//根结点的名	
	private $rowName='option';//要查询的XML的行名
	private $name_n='name';//要查询的行的名名
	private $val_n='value';//要查询的行的值名
	
	
//===========XML构造=================================================================
	//__get()方法用来获取私有属性
	public function __get($property_name){
		if(isset($this->$property_name)){
			return($this->$property_name);
		}else{
			return(NULL);
		}
	}
	
	//__set()方法用来设置私有属性
	public function __set($property_name, $value){
		$this->$property_name = $value;
	}

	//初始化，构造一个XML文档
	function startXML(){
		$this->dom=new DOMDocument('1.0','utf-8');
		$this->dom->formatOutput=true;
	}

		
	//生成XML,保存,$ele是生成的子元素
	function asXMLW($ele){
			$this->dom->appendChild($ele);//生成DOM
			if($this->savePath!=null){
				$result=$this->dom->save($this->savePath);//保存	
			}else{
				$result=$this->dom->saveXML();
			}
			return $result;
	}
	
	//没有用这个了，没有指定路径时直接返回字符串
	//生成XML时，若没有指定目录，给一个默认目录，即与本文件同目录
	function getdirXML(){
		$root=substr($_SERVER['DOCUMENT_ROOT'],0,strlen($_SERVER['DOCUMENT_ROOT'])-1);//根目录
		$cut=dirname($_SERVER["SCRIPT_NAME"])=='\\' ? '/':dirname($_SERVER["SCRIPT_NAME"]).'/';//主目录
		$file=date('YmdHis').'.xml';//文件名
		$dirXML=$root.$cut.$file;	
		return $dirXML;
	}

	
	//selectXML()查找数据时，所调用的函数，用来导入XML数据
	function intoXML(){
		if(is_string($this->sourceData)&&is_file($this->sourceData)){
			$xml=new DOMDocument();
			$xml->load($this->sourceData);
			$this->xml=$xml;			
		}elseif(is_a($this->sourceData,'DOMDocument')){		
			$this->xml=$this->sourceData;
		}else{
			exit("FlyXML::sourceData error!");
		}
		$this->row=$this->xml->getElementsByTagName($this->rowName);		
	}	
	
//===============生成DOM元素=======================================================
	//向XML转参
	function setDOM($eleArr){
		//创建值和属性	
		$this->element=$this->dom->createElement($eleArr['name']);//创建子元素
		if($eleArr['value']['type']==1){//写入值
			$title_v=$this->dom->createTextNode($eleArr['value']['val']);//创建元素值
			$this->element->appendChild($title_v);//子元素里写入值	
		}
		if($eleArr['value']['type']==2){//写入元素,如果有多个元素将是循环写入
			foreach($eleArr['value']['val'] as $k=>$v){
				$this->element->appendChild($v);
			}
		}

		foreach($eleArr['attr'] as $k=>$v){
			$lang_a=$this->dom->createAttribute($k);//创建属性
			$lang_av=$this->dom->createTextNode($v);//创建属性值
			$lang_a->appendChild($lang_av);//属性里写入值
			$this->element->appendChild($lang_a);//子元素里写入属性
		}
		
		return $this->element;
	}



//============生成attrXML=======================================================================================	
	//生成attrXML,传入类数据库返回数组
	function attrXml($stmt,$dbType){
		$this->startXML();
		if($dbType=='array'&&$this->sort==true){$stmt=$this->arraySort($stmt,$this->name_n);}
		$arr_row=array();//数据表里的每一行,只是数组，还不是DOM元素
		$arr_rows=array();//创建一个数组存放生成的元素,如果没有数据，它本来就是一空数组，也可以用	
		//从数据库里循环取出待生成DOM的数据
		for($i=0;$result=$this->fetchDB($stmt,$dbType);$i++){
			$arr_row[$i]=array();
			$arr_row[$i]['name']=$this->rowName;			
			$arr_row[$i]['value']=array('type'=>0,'val'=>'');		
			$arr_row[$i]['attr']=$result;
			
			//如需把哪个做值使用，如参考入下
			//$arr_row[$i]['value']=array('type'=>1,'val'=>$result['love']);
			//$arr_row[$i]['attr']=array_diff_key($result,array('love'=>''));			
		}

		
		//循环生成DOM元素
		foreach($arr_row as $k=>$v){
			$arr_rows[$k]=$this->setDOM($v);
		}

		$root=array(//设定一个根元素
			'name'=>$this->rootName,
			'value'=>array('type'=>2,'val'=>$arr_rows),
			'attr'=>$this->rootAttr
		);	
		
		$myxml=$this->setDOM($root);//生成根元素（写入了所有的子元素）	
		$dbType=='array'?$this->arrayNum=0:null;
		$dbType=='mysqli'?$stmt->data_seek(0):null;
		//pdo不能重能移动指针，若需要的话只能传入一个新的结果集		
		return $this->asXMLW($myxml);			
	}

//===========生成valXML========================================================================================

	//生成valXML,传入类数据库返回数组
	function valXML($stmt,$dbType){
		$this->startXML();
		if($dbType=='array'&&$this->sort==true){$stmt=$this->arraySort($stmt,$this->name_n);}
		$arr_row=array();//数据表里的每一行,只是数组，还不是DOM元素
		$arr_col=array();//单一行里的列数组DOM元素序列，这个数组里的元素，随着行循环，值不断的填充变化
		//$arr_col每次的生成内容都会写入$arr_rows里
		//$arr_rows各行里的行数组DOM元素序列，每个元素都是包含$arr_col里的列DOM序列
		$arr_rows=array();//创建一个数组存放生成的元素,如果没有数据，它本来就是一空数组，也可以用		
		for($i=0;$result=$this->fetchDB($stmt,$dbType);$i++){
			$arr_row[$i]=array();//每一行都是数组，元素为每一行数据	
			//把每一行的名值循环出来,每一行都是一个数组序列
			foreach($result as $key=>$value){		
				$arr_row[$i][$key]=array();//每一行的每一列都是数组，元素为名，值，属性，用于生成DOM
				$arr_row[$i][$key]['name']=$key;
				$arr_row[$i][$key]['value']=array('type'=>1,'val'=>$value);
				$arr_row[$i][$key]['attr']=array();
			}
			
			
			
			//每一行都是一个数组序列，循环生成DOM元素序列
			foreach($arr_row[$i] as $key=>$value){		
				$arr_col[$key]=$this->setDOM($value);
			}
			

			//为每一行的数组序列设定一个父元素，并将每一行都写入这个父元素
			$row=array(
				'name'=>$this->rowName,
				'value'=>array('type'=>2,'val'=>$arr_col),
				'attr'=>array()
			);
			
			$arr_col=array();
			//一定要清空，如果不清空的话，下一行会把上一行的元素插入，
			//当下一行也有的时候，在数组生成的时候会用下一行的覆盖上一行的，就不会再把上
			//一行的元素插入到下一行了，这里清空是指：把上一行的元素清空，只插入本行产生的元素
			//生成每一行的父元素（包含每一行的子元素）序列
			//每一行，循环的写入每一列
			$arr_rows[$i]=$this->setDOM($row);
		}

		$root=array(//设定一个根元素
			'name'=>$this->rootName,
			'value'=>array('type'=>2,'val'=>$arr_rows),
			'attr'=>$this->rootAttr
		);	
		
		$myxml=$this->setDOM($root);//生成根元素（写入了所有的子元素）	
		$dbType=='array'?$this->arrayNum=0:null;
		$dbType=='mysqli'?$stmt->data_seek(0):null;
		//pdo不能重能移动指针，若需要的话只能传入一个新的结果集
		return $this->asXMLW($myxml);		
	}

//=======对传入的数据源执行的处理函数============================================================================================

	//供attrXML/valXML向数据库取数据用，支持，pdo,mysqli两种方式
	function fetchDB($stmt,$dbType){
		switch($dbType){
			case 'pdo':
						$result=$stmt->fetch();break;
			case 'mysqli':
						$result=$stmt->fetch_assoc();break;		
			case 'array':
						$result=$this->arrayXML($stmt);break;
			default :
						$result=null;break;
		}
		return $result;
	}

	//数据源是数组时执行时的取值方法
	function arrayXML($arr1){
		if($this->arrayNum<count($arr1)){
			$result=$arr1[$this->arrayNum++];			
			return $result;			
		}else{
			return false;
		}
	}

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
//=======================================================================================
	
















	
	
	//------val--DOM取出所有的值--------------------------------------------------
	function DOMGetAllVal(){
		$arrXml=array();
		for($i=0;$i<$this->row->length;$i++){//在获取的行数内循环
			$arrXml[$i]=array();//创建一个数组，以便取得每一行的内容
			$rows=$this->row->item($i)->childNodes;//取得每一行里的各个元素
			for($j=1;$j<$rows->length;$j++){//在每一行的各个元素内循环
				$rowsOne=$rows->item($j);//取得指定的元素
				if($rowsOne->nodeType==1){//判断哪些元素是否为标签
					$key=$rowsOne->tagName;//取得标签名
					$value=$rowsOne->nodeValue;//取得标签值
					$arrXml[$i][$key]=$value;//记录标签的名和值
				}
			}
		}
		return $arrXml;//将所得元素打印出来
	}	
	
	
	
	//------attr--DOM取出所有的值--------------------------------------------------
	function DOMGetAllAttr(){
		$arrXml=array();
		for($i=0;$i<$this->row->length;$i++){//在获取的行数内循环
			$arrXml[$i]=array();//创建一个数组，以便取得每一行的内容
			$rows=$this->row->item($i)->attributes;//取得每一行里的各个元素
			foreach($rows as $k=>$v){
				$key=$v->name;//取得属性名
				$value=$v->value;//取得属性值
				$arrXml[$i][$key]=$value;//记录属性的名和值
			}
		}
		return $arrXml;//将所得元素打印出来
	}
	
	
//=========建增删改查=========================================================================	
	//创建XML
	function createXML($stmt,$dbType='array'){
			if($this->xmlType!='attr'&&$this->xmlType!='val'){
				return 'xmlType只能设定为attr,val,你设定为：'.$this->xmlType;
			}			
			if($this->xmlType=='attr'){
				return $this->attrXml($stmt,$dbType);
			}
			if($this->xmlType=='val'){
				return $this->valXml($stmt,$dbType);
			}
			return false;
	}


	//单点查询XML
	function callXML($name,$value=null){
		$result=null;
		$arr=$this->selectXML();
		foreach($arr as $key=>$val){
			if($name==$val[$this->name_n]){
				if($value!=null){//如果设定了值，则更改
					$arr[$key][$this->val_n]=$value;
					$this->createXML($arr,'array');
				}
				$result=$arr[$key][$this->val_n];
				return $result;
			}
		}
		return $result;
	}

	
	//根据数组子元素的值，查找数组的元素
	function selectXML($sel=null){
		$this->intoXML();
		$arr2=array();
		$res=null;
		if($this->xmlType!='attr'&&$this->xmlType!='val'){
			return 'xmlType只能设定为attr,val,你设定为：'.$this->xmlType;
		}
		if($this->xmlType=='attr'){
				$res=$this->DOMGetAllAttr();//读取XML所有
		}
		if($this->xmlType=='val'){
				$res=$this->DOMGetAllVal();//读取XML所有		
		}
		if($sel==null){
			return $res;
		}
		foreach($res as $k=>$v){
			if(in_array($v[$this->name_n],$sel)){
				$arr2[]=$res[$k];
			}
		}
		return $arr2;
	}	
	
	
//根据数组子元素的值，加数组的元素
function insertXML($upd){
	$arr=$this->selectXML();
	foreach($upd as $k=>$v){
		$arr[]=$upd[$k];
	}
	$this->createXML($arr,'array');
	return $arr;
}





//根据数组子元素的值，删除数组的元素
function deleteXML($del){
	$arr=$this->selectXML();
	$arr2=array();
	foreach($arr as $k=>$v){
		if(in_array($v[$this->name_n],$del)){
			unset($arr[$k]);
		}
	}
	foreach($arr as $key=>$value){
		$arr2[]=$value;//重新获取以免XML因unset后索引不符
	}	

	$this->createXML($arr2,'array');
	return $arr2;
}


//根据数组子元素的值，改数组的元素
function updateXML($upd){
	$arr=$this->selectXML();
	foreach($upd as $k=>$v){
			foreach($arr as $key=>$value){
				if($v[$this->name_n]==$value[$this->name_n]){
					$arr[$key]=$upd[$k];
				}
			}
	}
	$this->createXML($arr,'array',$this->savePath);
	return $arr;
}



	
}//class over

