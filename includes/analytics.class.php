<?php
class Analytics{

	public static function add($type, $user){
		global $path;

		$dir = $path . '/data/analytics/';
		$file = $dir . date('Y-m-j') . '.txt';

		$data = [
			'type' => $type,
			'heure' => date('H:i:s')
		];
		
		if($type == 'newSession'){
			$isMobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]) !== 0;

			$data += [
				'mobileDevice' => $isMobile,
				'statut' => $user->getStatut()
			];
		}
		
		if(!file_exists($dir)){
			mkdir($dir, 0774, true);
		}

		if(!file_exists($file)){
			$output = '';
		} else {
			$output = ',';
		} 

		$output .= json_encode($data);
		file_put_contents($file, $output, FILE_APPEND | LOCK_EX);
	}

	public static function getData(/*$start, $end*/){
		global $path;
		$dir = $path . '/data/analytics/';
		$file = $dir . date('Y-m-j') . '.txt';
		return json_decode('{"'. date('Y-m-j') . '":[' . file_get_contents($file) . ']}');
	}
}