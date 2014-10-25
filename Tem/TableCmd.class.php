<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//命令执行文件
class TableCmd extends FlyCmd{
	public function init(){
		C('DB_SET',array('type'=>'mysqli','host'=>'localhost','user'=>'root','pass'=>'xuyuan','name'=>'test'));
		$this->db=D();
	}
	public function run(){
		G('one');
		fly('^_^,welcom to use <table>');
		sleep(3);
		G('two');
		
		echo G('one','two');
	}
	
	
	public function dropTable(){
		$sql="
			drop table if exists orderform;
		";
		$result=$this->db->exec($sql);
		return $result;
	}
	
	public function createTable(){
		$sql="
			create table if not exists orderform(
				id int unsigned not null auto_increment,
				username varchar(50) default '' not null,
				sex char(1) default '' not null,
				telephone varchar(30) default '' not null,
				amount float(9,3) not null,
				info text,
				email varchar(200) default '' not null,
				createtime datetime not null,
				updatetime timestamp not null,
				primary key(id)
			);
		";
		$result=$this->db->exec($sql);		
		return $result;
		
	}
	
	public function insert(){
		
		while(true){		
			$result=$this->db->table('orderform')->selectCount();
			
		
			/*
			if($result>50000){
				
			}
			*/
			
			$arrs=$this->order(5000);
			$result=$this->db->table('orderform')->insert($arrs);
			print_r($result);br();
			sleep(1);
		}
		
	}
	
	
	public function update(){
		$arr=array(
			'sex'=>'女'
		);
		$result=$this->db->table('orderform')->where('id=3')->update($arr);
		print_r($result);
	}


	
	
	public function order($all=1){
		$arrs=array();
	
		for($i=0;$i<$all;$i++){	
			//username
			$username=array('韩','梅','李','雷','小','丸','子','妖','若','寒','秋','凝','晓','园','梨','白');
			$key=array_rand($username,2);
			$arr['username']=$username[$key[0]].$username[$key[1]];
			
			//sex
			$sex=array('男','女');
			$arr['sex']=$sex[array_rand($sex)];
			
			//telephone
			$arr['telephone']=mt_rand(10000000,99999999);
			
			//email
			$email='';
			for($j=0;$j<8;$j++) $email.=chr(rand(97,122));
			$arr['email']=$email.'@me.com';
			
			
			//amount
			$arr['amount']=mt_rand(0,999999)/1000;

			//info
			$info=array('good','very good','差不多','还行','不错','没意思','下次再用','值得','非常不错','不值得呀');
			$arr['info']=$info[array_rand($info)];

		
			$arrs[]=$arr;
			
		}
		G('two');
		return $arrs;
	}
	
	
}