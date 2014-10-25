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
class FlyPdo{
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
	
		//参数设置
		$dbSet['opt']=array(
				PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,//异常模式		
			    //PDO::ATTR_PERSISTENT=>true,//是否开启持久连接,即程序执行完之后仍不消毁
				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true,
				PDO::ATTR_AUTOCOMMIT=>true,//是否自动提交,默认开启
				PDO::ATTR_CASE=>PDO::CASE_LOWER,//表单列强制取出来不大/小/不变
				PDO::ATTR_ORACLE_NULLS=>PDO::NULL_NATURAL,//如何处理数据空白
				PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,//取出数据的模式
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			);
		//生成数据库连接
		try{
			$db=new PDO($dbSet['dsn'],$dbSet['user'],$dbSet['pass'],$dbSet['opt']);
		}catch(PDOException $e){
			$this->doException("ERROR:".$e->getMessage()."---LINE:".$e->getLine());
		} 
		$this->db=$db;;
	}
	
	

	//数据库查询操作,返回结果集,返回给调用的应该是直接的数据，所以不要提供直接调用
	public function query($str,$info=''){
		try{
			$stmt=$this->db->query($str);
			return $stmt;
		}catch(PDOException $e){
			$this->doException('ERROR('.$info.'): '.$e->getMessage().'---'.$str.'---');
			return false;
		}
	}
	
	
	//数据库执行操作,返回影响行数
	public function exec($str,$info=''){
		try{
			$result=$this->db->exec($str);
			return $result;
		}catch(PDOException $e){
			$this->doException('ERROR('.$info.'): '.$e->getMessage().'---'.$str.'---');
			return false;
		}
	}


	//增
	public function insert($sql){
		return $this->exec($sql);
	}
	
	//删
	public function delete($sql){
		return $this->exec($sql);	
	}
	
	//改
	public function update($sql){
		return $this->exec($sql);
	}
	
	public function selectOne($sql){
		$stmt=$this->query($sql);
		return $stmt->fetch();
	}


	//取出所有数据集
	public function selectAll($sql){
		$stmt=$this->query($sql);
		return $stmt->fetchAll();
	}
	
	//取出行数
	public function selectCount($sql){
		$stmt=$this->query($sql);
		return $stmt->rowCount();	
	}
	
	//取出所有数据及行数
	public function selectBoth($sql){
		$stmt=$this->query($sql);
		$count=$stmt->rowCount();
		$all=$stmt->fetchAll();
		return array('count'=>$count,'all'=>$all);
	}
	



//=====================================================================


	//获取下一个增值id模型
	public function nextid($sql) {
		$stmt=$this->query($sql);
		$result = $stmt->fetch();
		return $result['auto_increment'];
	}
	
	


	
	

	
}