<?php
require_once "$path/includes/default_config.php";
class Analytics{

	public static function add($type, $user = ''){
		global $path;
		global $Config;

		if($Config->analystics_interne == false){
			return;
		}

		$dir = $path . '/data/analytics/';
		$file = $dir . date('Y-m-d') . '.txt';

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

	public static function getData($start, $end){
		global $path;
		$dir = $path . '/data/analytics/';

		$start_date = date_create($start);
		$end_date   = date_create($end);
		$end_date->modify('+1 day');

		$interval = DateInterval::createFromDateString('1 day');
		$daterange = new DatePeriod($start_date, $interval ,$end_date);

		echo '{';
		foreach($daterange as $date) {
			$dateStr = $date->format('Y-m-d');
			$file = $dir . $dateStr . '.txt';

			if(file_exists($file)) {
				echo '"' . $dateStr . '":[' . file_get_contents($file) . '],';
			} else {
				echo '"' . $dateStr . '":[],';
			}
			ob_flush();
			flush();
		}
		echo '"1986-10-24":[] }';
		die();
	}
}