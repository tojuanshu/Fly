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
class FlyDB{
	public $db=null;
	public $table='';
	public $where='';
	public $limit='';
	public $order='';
	public $group='';
	public $show=false;
	
	private $db_dsn;		//DSN
	private $db_host;		//主机名
	private $db_user;		//用户名
	private $db_pass;		//用户密码
	private $db_name;		//连接数据库名
	


	//添加数据库执行错误时的处理方法
	public function doException($err){
		throw new Exception($err);
		//exit($err);
	}

	//传入数据参数，数据库连接信息
	public function __construct($dbSet=false){
		if(!is_array($dbSet)){
			exit("please input: array('host'=>,'user'=>,'pass'=>,'name'=>)");
		}
	
		//实例化的时候，获取数据库连接参数
		$dbSet['dsn']='mysql:host='.$dbSet['host'].';dbname='.$dbSet['name'];
		
		$this->dbType=empty($dbSet['type'])?'pdo':$dbSet['type'];
		$class='Fly'.ucfirst($this->dbType);
		$this->db=new $class($dbSet);
		//$this->pdo=$this->PDO();
	}
	


	//数据库查询操作
	public function query($str,$info=''){
		return $this->db->query($str,$info);
	}
	
	
	//数据库执行操作
	public function exec($str,$info=''){
		return $this->db->exec($str,$info);
	}
	
	
	
	public function table($table){
		$this->table=$table;
		return $this;
	}
	
	public function where($where){
		$this->where=$where;
		return $this;
	}
	
	public function limit($limit){
		$this->limit=$limit;
		return $this;
	}
	
	public function order($order){
		$this->order=$order;
		return $this;
	}	
	
	public function group($group){
		$this->group=$group;
		return $this;
	}		
	
	public function show($show=true){
		$this->show=$show;
		return $this;
	}
//============================================================================	
	
	//增
	public function insert($arr){
		if(isset($arr[0]) && is_array($arr[0])){
			$names=$datas='';
			foreach($arr as $key0 => $value0){
				$name=$data='';
				foreach($value0 as $key => $value){
					$name.='`'.$key.'`,';
					$data.='"'.$value.'",';
				}
				$names=trim($name,',');
				$datas.=',('.trim($data,',').')';
							
			}
			$datas=trim($datas,',');
			$sql="insert into `{$this->table}` ({$names}) values {$datas};";
		}else{
			$name=$data='';
			foreach($arr as $key => $value){
				$name.='`'.$key.'`,';
				$data.='"'.$value.'",';
			}
			$name=trim($name,',');
			$data=trim($data,',');
			$sql="insert into `{$this->table}` ({$name}) values ({$data})";
		}
		if($this->show) return $sql;
		return $this->db->insert($sql);
	}
	
	//删
	public function delete(){
		$sql="delete from `{$this->table}`";
		if($this->where){
			$sql.=" where {$this->where}";
		}else{
			$sql.=" where 1=0";
		}
		if($this->show) return $sql;
		return $this->db->delete($sql);	
	}
	
	//改
	public function update($arr){
		$column='';
		foreach($arr as $key => $value){
			if(is_string($value)) $value='"'.$value.'"';
			$column.='`'.$key.'` = '.$value.',';
			
		}
		$column=trim($column,',');
		//echo "update `{$table}` set {$column} where {$where}";exit;
		$sql="update `{$this->table}` set {$column}";
		if($this->where){
			$sql.=" where {$this->where}";
		}else{
			$sql.=" where 1=0";
		}
		if($this->show) return $sql;
		return $this->db->update($sql);
	}
	
	
	public function selectSql($arr){
		$column='';
		if(empty($arr)){
			$column='*';
		}else{
			foreach($arr as $key => $value){
				$column.='`'.$value.'`,';
			}	
			$column=trim($column,',');
		}
		$sql="select {$column} from `{$this->table}`";
		if($this->where){
			$sql.=" where {$this->where}";
		}

		if($this->group){
			$sql.=" group by {$this->group}";
		}
		
		if($this->order){
			$sql.=" order by {$this->order}";
		}		

		if($this->limit){
			$sql.=" limit {$this->limit}";
		}
		return $sql;	
	}
	
	//查
	public function select($arr=''){
		$sql=$this->selectSql($arr);
		if($this->show) return $sql;
		return $this->db->selectAll($sql);		
	}
	
	public function selectOne($arr=''){
		$this->limit='1';
		$sql=$this->selectSql($arr);
		if($this->show) return $sql;
		return $this->db->selectOne($sql);		
	}
	

	public function selectCount($arr=''){
		$sql=$this->selectSql($arr);
		if($this->show) return $sql;
		return $this->db->selectCount($sql);	
	}


	public function selectAll($arr=''){
		$sql=$this->selectSql($arr);
		if($this->show) return $sql;
		return $this->db->selectAll($sql);	
	}	

	public function selectBoth($arr=''){
		$sql=$this->selectSql($arr);
		if($this->show) return $sql;
		return $this->db->selectBoth($sql);	
	}	

	

	//取出1条记录
	public function queryOne($sql){
		return $this->db->selectOne($sql);
	}	
	

	//取出所有结果集
	public function queryAll($sql){
		return $this->db->selectAll($sql);
	}


	//取出行数
	public function queryCount($sql){
		return $this->db->selectCount($sql);
	}
	

	//取出所有数据及行数	
	public function queryBoth($sql){
		return $this->db->selectBoth($sql);
	}	

	
//=================================================================


	//获取下一个增值id模型
	public function nextid($table) {
		$sql="SHOW TABLE STATUS LIKE '$table'";
		return $this->db->nextid($sql);
	}




	
}