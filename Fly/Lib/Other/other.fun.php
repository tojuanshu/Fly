<?php

function load_other($load=false){
	$files=array(
		'FlyTest'			=>__DIR__.'/FlyTest.class.php',
	);
	if($load){
		foreach($files as $file){
			require($file);
		}
	}
	return $files;
}
