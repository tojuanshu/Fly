<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
//命令执行文件,采集流程
class PluckCmd extends FlyCmd{
	public function run(){
		$http=new FlyHttp();
		$url='http://www.baidu.com';
		
		$rule=array('first'=>false,'page'=>'?;offset=@;order=ident;sort=ASC');
		//取得urls
		$result=$this->getUrls();
		//1遍历
		foreach($result as $key => $value){
			$askdates=date('Y-m-d H:i:s');//此次更新时间，下次访问时间
			$status=array('status'=>'10','tag'=>'成功','askdates'=>$askdates,'id'=>$value['id']);






			
			//2分页header
			for($i=1;$i<300;$i++){
				$url=FlyPluck::nextPage($value['url'],$rule,$i);

			
			
			
			
			
			
			
			
			
				//3采集
				if($http->getUrl($url)!='200'){$status=array();break;}else{//采集
					//匹配
					if(!FlyPluck::match($reg,$http->content,$rule)){$status=array();break;}else{
						//过虑，增减数据，完整性处理，
						//显示
						//过库
						$this->pdo->insertTb('flight_tbl',FlyTool::sqlFilter($datas));		
					}
				}









				
				//分页footer
				usleep(300000);
			}








			
			$this->returnUrl($status);usleep(300000);//回复url					
		}$this->returnUrls();$this->freeProcess();//回复urls,置进程为空闲		
	}
}