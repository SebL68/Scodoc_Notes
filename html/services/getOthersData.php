<?php
	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');
	$dir = $path . '/data/analytics/';
	$file = $dir . 'othersData.json';
	
	if(!file_exists($dir)) {
		mkdir($dir, 0774, true);
	}

	if(file_exists($file)) {
		if(filesize($file) > 1000000) {
			die(); // Trop de data, il y a quelque chose qui cloche !
		}
		$data = json_decode(file_get_contents($file, true), true);
	} else {
		$data = [];
	}

	$_GET["last_sent"] = time();
	$data[$_GET['name']] = $_GET;
	file_put_contents($file, json_encode($data));