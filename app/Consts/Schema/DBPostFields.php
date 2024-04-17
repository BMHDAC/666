<?php

namespace App\Consts\Schema;
use App\Consts\DbTypes;

abstract class DBPostFields
{
	const POST = [
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
		'name' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'description' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
	];
}