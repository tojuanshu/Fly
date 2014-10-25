<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
/*
include _logErr<fun>,_log<fun>
*/
class FlyMysql{
	public $db=null;

	//添加数据库执行错误时的处理方法
	public function doException($err){
		throw new Exception($err);
		//exit($err);
	}

	//传入数据参数，数据库连接信息
	public function __construct($dbSet=false){
		if(!is_array($dbSet)){
			exit("please input：array('host'=>,'user'=>,'pass'=>,'name'=>)");
		}
		$this->db=mysql_connect($dbSet['host'],$dbSet['user'],$dbSet['pass']);//连接数据库
		
		if(!$this->db){
			$this->doException('ERROR:'.mysql_errno() . ": " . mysql_error());//连接错误，抛出异常
		}

		if(!empty($dbSet['name'])){
			mysql_select_db($dbSet['name'],$this->db);
		}
		
		if(mysql_errno($this->db)){
			$this->doException('ERROR:'.mysql_errno() . ": " . mysql_error());
		}
		return $this;
	}
	
	


	//数据库查询操作,返回结果集
	public function query($str,$info=''){
		if($stmt=mysql_query($str,$this->db)){
			return $stmt;
		}else{
			$this->doException('ERROR:'.mysql_errno() . ": " . mysql_error().'---'.$str.'---');
			return false;
		} 		

	}

	//执行SQL,返回影响的行数
	public function exec($str,$info=''){
		if($stmt=mysql_query($str,$this->db)){
			return mysql_affected_rows($this->db);
		}else{
			$this->doException('ERROR:'.mysql_errno() . ": " . mysql_error().'---'.$str.'---');
			return false;
		} 
	}	
	


	//增
	public function insert($sql){
		return $this->exec($sql);
	}

	//改
	public function update($sql){
		return $this->exec($sql);
	}	
	
	//增
	public function delete($sql){
		return $this->exec($sql);
	}	
	
	
	
	//取出1条记录
	public function selectOne($sql){
		$stmt=$this->query($sql);
		$results=array();
		if($result=mysql_fetch_assoc($stmt)){
			$results=$result;
		}
		return $results;
	}	
	

	
	//取出所有结果集
	public function selectAll($sql){
		$stmt=$this->query($sql);
		$results=array();
		while($result=mysql_fetch_assoc($stmt)){
			$results[]=$result;
		}
		return $results;
	}	

	//取出行数
	public function selectCount($sql){
		$stmt=$this->query($sql);
		return mysql_num_rows($stmt);
	}

	
	//取出数据和总行数
	public function selectBoth($sql){
		$stmt=$this->query($sql);
		$count=mysql_num_rows($stmt);
		$all=array();
		while($result=mysql_fetch_assoc($stmt)){
			$all[]=$result;
		}
		return array('count'=>$count,'all'=>$all);	
	}
	
	
	
	//=====================================================================	
	
	//获取下一个增值id模型
	public function nextid($sql){
		$stmt=$this->query($sql);
		$result = mysql_fetch_assoc($stmt);
		return $result['Auto_increment'];
	}
	

	
}