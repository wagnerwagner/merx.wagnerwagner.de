<?php
return [
	'debug' => true,
	'wagnerwagner.merx.production' => false,
	'nova-links' => true,
	'cache.site.search' => false,
	// 'cache.pages' => false,
	'email' => [
		'transport' => [
			'type' => 'smtp',
			'host' => 'mail.test',
			'port' => 1025,
			'security' => false,
		],
	],
];
