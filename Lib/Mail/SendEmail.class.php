<?php

/*
		array(
			'email'=>'',
			'subject'=>'',
			'body'=>''
		)
*/

class SendEmail{
	public $mail;


	//初始化邮箱信息
	public function __construct($config=null){
		//require FLY_VENDOR.'PHPMailer-master/PHPMailerAutoload.php';	
		$this->mail = new PHPMailer;
		if($config){
			$this->mail->Username = $config['username'];          // SMTP username
			$this->mail->Password = $config['password'];                     // SMTP password
			$this->mail->From = $config['from'];
		}else{
			$this->mail->Username = 'xuyuan_me@163.com';          // SMTP username
			$this->mail->Password = 'xuyuan';                     // SMTP password
			$this->mail->From = 'xuyuan_me@163.com';			
		}
		//$this->mail->SMTPSecure = 'tls';                    	// Enable encryption, 'ssl' also accepted

		
		$this->mail->CharSet='UTF-8';//设置编码以支持中文（不设好像也可以）
		$this->mail->isSMTP();                                // Set mailer to use SMTP
		$this->mail->Host = 'smtp.163.com';  					// Specify main and backup server
		$this->mail->SMTPAuth = true;                         // Enable SMTP authentication

		$this->mail->FromName = 'qycn(test)';
		$this->mail->WordWrap = 50;                           // Set word wrap to 50 characters
		$this->mail->isHTML(true);                            // Set email format to HTML	
	}

	
	//邮件发送
	public function send($msg){

		$this->mail->addAddress($msg['email']);//邮箱
		$this->mail->Subject=$msg['subject'];//主题
		$this->mail->Body=$msg['body'];//内容
		
		if(!$this->mail->send()){
			$status='NG: '.$this->mail->ErrorInfo;
		}else{
			$status='OK';
		}	
		
		$info=date('Y-m-d H:i:s').'=>'.$msg['email'].'('.$status.')';
		
		$this->over($info);
		if($status=='OK'){
			return true;
		}else{
			return false;
		}
		
	}

	//清除收件人
	public function clear(){
		$this->mail->ClearAllRecipients();		
	}
	
	
	
	public function over($info){
		FlyLog::record($info,'mail.log');
	}

}

