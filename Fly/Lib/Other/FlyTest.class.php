<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
/*
include:hostToIp<fun>,
HTTP控制类
$code=$http->getUrl()/postUrl()/headUrl();
if($code=='200'){
	$this->request/response/content;
	$this->getCharset()/getKeyword()/getDebug()
}



*/
class FlyHttp{	
	public $urls;						//解析后的URL信息
	public $code='999';					//响应码
	public $charset='unknow';			//文档编码
	public $keyword='unknow';			//文档关键字
	public $location;					//跳转地址
	
	public $ctimeout=15;				//connection timeout
	public $atimeout=15;				//ask timeout

	public $request;					//请求头
	public $response;					//响应头
	public $content;					//响应内容 

	public $startTime;					//采集开始时间
	public $startTime2;					//每一过程起始进间
	public $message='';					//执行状态信息
	public $infos=array();				//执行流程详情
	public $localDNS=false;				//是否本地DNS解析	
	public $debug=true;					//是否开启调试模式	
	public $host=false;					//是否启动host配置
	public $jump=false;					//301,302跳转
	
//默认请求头信息----------------------------------------	
	public $line=array('method'=>'GET','paths'=>'/','version'=>'HTTP/1.1');				
	public $row=array(
				'Host'=>'localhost',
				'User-Agent'=>'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0',
				'Connection'=>'Close'
			);
	public $get=array();
	public $cookie=array();	
	public $post=array();
	

//--------------------------------------------------	
	
	public function __construct(){
		$this->response='code:999';
	}

	//head方法访问,初始化
	public function headUrl($url,$cookie=null){
		$this->line['method']='HEAD';
		if($get){
			$this->get=array_merge($this->get,$get);//合并信息
		}			
		if($cookie){
			$this->cookie=array_merge($this->cookie,$cookie);//合并信息
		}
		return $this->run($url,'head');
	}
	
	//get方法访问,初始化
	public function getUrl($url,$get=null,$cookie=null){
		$this->line['method']='GET';
		if($get){
			$this->get=array_merge($this->get,$get);//合并信息
		}		
		if($cookie){
			$this->cookie=array_merge($this->cookie,$cookie);//合并信息
		}		
		return $this->run($url,'get');
	}
	
	//post方法访问,初始化
	public function postUrl($url,$post=null,$cookie=null){
		$this->line['method']='POST';
		if($post){
			$this->post=array_merge($this->post,$post);//合并信息
		}
		if($cookie){
			$this->cookie=array_merge($this->cookie,$cookie);//合并信息
		}
		return $this->run($url,'post');
	}


	//获取调试信息 
	public function getDebug(){
		$info='';
		$info.="(request)\r\n".$this->request."\r\n\r\n(response)\r\n".$this->response."\r\n\r\n(recode)\r\n";
		foreach($this->infos as $key => $value){
			$info.=str_pad($value['name'].': '.$value['msg'],50).'|'.$value['time']."\r\n";
		}
		return PHP_SAPI=='cli'?$info:nl2br($info);
	}

	
	
	//获取文档编码
	public function getCharset(){
		if($this->charset!='unknow') return $this->charset;
		$charset=array('utf-8','gbk','gb2312');
		$reg='/'.implode('|',$charset).'/i';
		if($value=preg_match($reg,$this->response.$this->content,$arr)){
			$charset=strtolower($arr[0]);
		}else{
			$charset='unknow';
		}
		$this->charset=$charset;
		return $this->charset;
	}

	
	//从网页头信息中找出关键字
	public function getKeyword(){
		if($this->keyword!='unknow') return $this->keyword;
		if(preg_match_all("
						/<\s*meta\s.*?(keywords|other).*?content\s*=\s*		#查找标识
						([\"\'])?											#是否有前引号
						(?(2) (.*?)\\2 | ([^\s\>]+))						#根据是否有前引号匹配内容
						/isx",$this->content,$keywords,PREG_PATTERN_ORDER)){					
		$keyword=implode(',',$keywords[3]);
		}else if(preg_match("/<\s*title\s*>(.*?)<\s*\/\s*title\s*>/is",$this->content,$keywords)){
			$keyword=$keywords[1];
		}else{
			$keyword='unknow';
		}
		$this->keyword=$keyword;
		return $this->keyword;
	}	
	


//==================================================================================


	
	//运行入口
	private function run($url,$type='get'){
		$this->start();
		$go=true;$this->startTime2=$this->startTime=microtime(true);//设定启动时间
		$go?$go=$this->parseUrl($url):null;//解析URL
		$go?$go=$this->parseDomain():null;//解析域名	
		$go?$go=$this->setRequest():null;//设定请求		
		$go?$go=$this->connect():null;//建立连接
		$go?$go=$this->writeRequest():null;//写入请求
		$go?$go=$this->readResponse():null;//读取头部
		$go?$go=$this->readContent($type):null;//读取全文		
		$go?$go=$this->close():null;//关闭连接
		$this->over();
		//return $go;
		return $this->code;
	}
	

	//解析URL
	private function parseUrl($url){
		if(!preg_match('/^http:\/\//i',$url)) $url='http://'.$url;
		$urls= parse_url($url); 
		!isset($urls['scheme']) && $urls['scheme'] = 'http'; 	//获取协议
		!isset($urls['host']) && $urls['host'] = '';   		 	//获取主机
		!isset($urls['path']) && $urls['path'] = '/';   		//获取路径
		!isset($urls['query']) && $urls['query'] = '';   		//获取参数
		!isset($urls['port']) && $urls['port'] = '80'; 			//获取端口	

		//添加GET参数 
		if (count($this->get)>0){
			parse_str($urls['query'],$output);//解析字符串为数组
			$output=array_merge($output,$this->get);//添加想要的参数
			$urls['query']=trim(http_build_query($output));//重新生成查询字符串			
		}
		
		$urls['paths'] = $urls['path'].($urls['query'] ? '?'.$urls['query'] : ''); //组拼完整路经  				
		$this->urls=$urls;
		$this->record('parseUrl','ok');
		return true;
	}	
	
	
	//解析域名
	private function parseDomain(){
		//是否启动本地域名解析
		//$this->urls['ip']='121.10.139.10';
		if($this->host){
			$this->urls['ip'] = hostToIp($this->urls['host']);	//配置获取IP	
		}else{
			$this->urls['ip'] = trim(gethostbyname($this->urls['host']));	//自动获取IP		
		}
			
		$this->record('parseDomain','ok('.$this->urls['ip'].')');
		return true;
	}
	
	
	//设定请求
	private function setRequest(){
		//准备部分的请求头	
		$port=$this->urls['port']=='80'?'':':'.$this->urls['port'];
		$this->row['Host']=$this->urls['host'].$port;	
		$this->line['paths']=$this->urls['paths'];
		//请求行
		$line = $this->line['method'].' '.$this->line['paths'].' '.$this->line['version']."\r\n";		

		//POST值
		$post='';
		if($this->line['method']=='POST'){
			if ( count($this->post) > 0 ) {
				foreach ( $this->post as $postKey => $postVal ) {
					$post.= $postKey."=".urlencode($postVal)."&";
				}
				$post= substr($post,0,-1);
				$length=strlen($post);
				$this->row['Content-Type']='application/x-www-form-urlencoded';
				$this->row['Content-Length']=$length;				
			}
		}

		//COOKIE值
		$cookie='';
		if ( count($this->cookie) > 0 ) {
			$cookie= 'Cookie: ';
			foreach ( $this->cookie as $cookieKey => $cookieVal ) {
				$cookie.= $cookieKey."=".urlencode($cookieVal)."; ";
			}
			$cookie= substr($cookie,0,-2) . "\r\n";
		} 		
		
		//请求域
		$row='';
		if(count($this->row) > 0 ){
			foreach($this->row as $rowKey => $rowVal)
				$row .= $rowKey.": ".$rowVal."\r\n";
		}
		

		$headers=$line.$row.$cookie."\r\n".$post;
		
		$this->request=$headers;
		$this->record('setRequest','ok');
		return true;
	}	


	
	//建立连接
	private function connect(){
		$fp=@fsockopen($this->urls['ip'],$this->urls['port'],$errno,$erron,$this->ctimeout);
		if(!$fp){
			$this->record('connect','ng!');
			return false;
		}		

		//设定参数
		stream_set_blocking($fp,true);//设置为阻塞模式
		stream_set_timeout($fp,$this->atimeout);//设置超时
		$this->fp=$fp;
		$this->record('connect','ok');
		return true;
	}

	
	
	//写入请求头
	private function writeRequest(){
		$write=fwrite($this->fp,$this->request);
		if(!$write){
			$this->record('writeRequest','ng');
			@fclose($this->fp);
			return false;
		}	
		$this->record('writeRequest','ok');
		return true;
	}
	

	
	//获取HTTP头
	private function readResponse(){
		//确认资源句柄
		if(!is_resource($this->fp)){
			$this->record('readResponse','no resource handle!');
			return false;
		}	
		
		//读取HTTP头
		$header='';
		while(!feof($this->fp)){
			$headerRow = fgets($this->fp);
			if($this->checkTimeout($this->fp)){//是否超时后进来
				$this->record('readResponse','read time out(http)!');
				@fclose($this->fp);
				return false;
			}
		
			//匹配响应状态
			if(preg_match("/^HTTP\/[^\s]+\s+([^\s]+)\b/",trim($headerRow), $status)){
				$this->code=$status[1];				
			}

			//处理301，302 获取跳转地址
			if(preg_match("/^(Location:|URI:)\s*(.*)/i",trim($headerRow),$location)){
				$this->location=$location[2];	
			}
			
			
			if(trim($headerRow)==''){
				break;
			}else{
				$header.=$headerRow;
			}	
		}
		$this->response=$header;
		/*
		if($this->jump&&($this->code=='301' || $this->code=='302')&&$this->location){
				
				return($this->getUrl($this->location));
		}
		*/
		if($this->code!='200'){
			$this->record('readResponse','failure: '.$this->code.' '.$this->location);
			@fclose($this->fp);
			return false;
		}
		$this->record('readResponse','ok: '.$this->code.' '.$this->location);
		return true;
	}


	
	
	//获取HTML
	private function readContent(){
		if($this->line['method']=='HEAD'){
			$this->record('readContent','hand request!');
			return false;
		}
	
		//确认资源句柄
		if(!is_resource($this->fp)){
			$this->record('readContent','no resource handle!');
			return false;
		}	
		
		//取回正文信息
		$content='';
		while (!feof($this->fp)) {
			if($this->checkTimeout($this->fp)){
				$this->record('readContent','read time out(html)!');
				@fclose($this->fp);
				return false;
			}
			$content .= fread($this->fp, 512);
		}		
		$this->content=$content;
		$content=trim($content);
		$this->record('readContent','ok');		
		return true;
	}

			

	//关闭连接
	private function close(){
		//结束
		@fclose($this->fp);	
		$this->record('close','ok');
		return true;
	}

	
	//记录运行过程 
	private function record($name,$message){
		$this->message=$name.'=>'.$message;
		if($this->debug){			
			$this->infos[]=array('name'=>$name,'msg'=>$message,'time'=>$this->difTime());
		}
	}
	
	
	//计算请求时间差
	private function difTime($start=null,$end=null){
		if(!$start) $start=$this->startTime2;
		if(!$end) $end=microtime(true);
		$dif=round(($end-$start),4);
		$this->startTime2=$end;
		return $dif.'s';
	}	
	
	private function start(){
		$this->infos=array();
		$this->urls=array();				//解析后的URL信息
		$this->code='999';					//响应码
		$this->charset='unknow';			//文档编码
		$this->keyword='unknow';			//文档关键字
		$this->location='';					//跳转地址


		$this->request='';					//请求头
		$this->response='';					//响应头
		$this->content='';					//响应内容 		
		
	}
	
	private function over(){
		$this->endTime=microtime(true);//设定结束时间
		$start=date('Y-m-d H:i:s',$this->startTime);
		$end=date('Y-m-d H:i:s',$this->endTime);
		$this->infos[]=array('name'=>'over','msg'=>$start.'->'.$end,'time'=>$this->difTime($this->startTime,$this->endTime));
	}
	
	

	
	
	
	//检查数据读取超时与否
	private function checkTimeout($fp){
			$info=stream_get_meta_data($fp);//读取失败，检测读取状态	
			if($info['timed_out']){
				return true;
			}			
			return false;
	}



}//class over



