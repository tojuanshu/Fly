<?php

//把文件注册到系统
function load_mail($load=false){
	require __DIR__.'/PHPMailer-master/PHPMailerAutoload.php';	
	$files=array(
		'SendEmail'=>	__DIR__.'/SendEmail.class.php',
	);
	if($load){
		foreach($files as $file){
			require($file);
		}
	}
	return $files;
}








