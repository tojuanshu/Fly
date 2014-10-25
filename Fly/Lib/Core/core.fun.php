<?php
function load_core($load=false){
	$files=array(
	'FlyApp'			=>__DIR__.'/FlyApp.class.php',
	'FlyCmd'			=>__DIR__.'/FlyCmd.class.php',
	'FlyingCmd'			=>__DIR__.'/FlyingCmd.class.php',
	'FlyInstallCmd'		=>__DIR__.'/FlyInstallCmd.class.php',
	);
	if($load){
		foreach($files as $file){
			require($file);
		}
	}
	return $files;
}