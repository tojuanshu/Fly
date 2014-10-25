<?php

function load_cli($load=false){
	$files=array(
		'FlyCli'	=>	__DIR__.'/FlyCli.class.php'	
	);
	if($load){
		foreach($files as $file){
			require($file);
		}
	}
	return $files;
}
