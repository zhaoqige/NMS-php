<?php

$data = array(
	'map' => array(
		'zoom' => 18,
		'center' => array(
			'lat' => (39.92615+39.9256)/2,
			'lng' => (116.5612+116.5599)/2
		)
	),
	'points' => array(
		array(
			'status' => 'level2',
			'level' => 2,
			'sn' => '47268242000018',
			'name' => '南门',
			'pos' => array(
					'lat' => 39.92575,
					'lng' => 116.5612
			),
			'noise' => '60db',
			'temp' => '0.9  ℃, 24.6 %RH',
			'pm' => '5.0 μg/m3, 5 μg/m3',
			'ts' => date('Y-m-d H:i:s')
		),
		array(
			'status' => 'level1',
			'level' => 1,
			'sn' => '47268242000014',
			'name' => '北门',
			'pos' => array(
				'lat' => 39.92605,
				'lng' => 116.5599
			),
			'noise' => '60db',
			'temp' => '1.2  ℃, 24.6 %RH',
			'pm' => '5.0 μg/m3, 5 μg/m3',
			'ts' => date('Y-m-d H:i:s')
		)
	)
);

echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>
