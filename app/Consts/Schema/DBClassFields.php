<?php

namespace App\Consts\Schema;
use App\Consts\DbTypes;

abstract class DBClassFields
{
	const CLASSROOM = [
		'created_at' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'updated_at' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'id' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'class' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'id_teacher' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
	];
}
