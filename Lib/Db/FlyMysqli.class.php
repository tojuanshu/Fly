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
class FlyMysqli{
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
		@$mysqli = new mysqli($dbSet['host'],$dbSet['user'],$dbSet['pass'],$dbSet['name']);//连接数据库
		if($mysqli->connect_errno){
			$this->doException('ERROR:'.$mysqli->connect_error);//连接错误，抛出异常
		}
		$this->db=$mysqli;//连接正确，记录连接句柄  
	}
	
	


	//数据库查询操作,返回结果集
	public function query($str,$info=''){
		if($stmt=$this->db->query($str)){
			return $stmt;
		}else{
			$this->doException('ERROR('.$info.'): '.$this->db->error.'---'.$str.'---');
			return false;
		}
	}

	//执行SQL,返回影响的行数
	public function exec($str,$info=''){
		if($stmt=$this->db->query($str)){
			return $this->db->affected_rows;
		}else{
			$this->doException('ERROR('.$info.'): '.$this->db->error.'---'.$str.'---');
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
		if($result=$stmt->fetch_assoc()){
			$results=$result;
		}
		return $results;
	}	
	

	
	//取出所有结果集
	public function selectAll($sql){
		$stmt=$this->query($sql);
		$results=array();
		while($result=$stmt->fetch_assoc()){
			$results[]=$result;
		}
		return $results;
	}	

	//取出行数
	public function selectCount($sql){
		$stmt=$this->query($sql);
		return $stmt->num_rows;
	}

	
	//取出数据和总行数
	public function selectBoth($sql){
		$stmt=$this->query($sql);
		$count=$stmt->num_rows;
		$all=array();
		while($result=$stmt->fetch_assoc()){
			$all[]=$result;
		}
		return array('count'=>$count,'all'=>$all);	
	}
	
	
	
	//=====================================================================	
	
	//获取下一个增值id模型
	public function nextid($sql){
		$stmt=$this->query($sql);
		$result = $stmt->fetch_assoc();
		return $result['Auto_increment'];
	}
	

	
}