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
		'media' => [
			'type' => DbTypes::JSON,
			'cache' => true,
		],
		'status' => [
			'type' => DbTypes::INT,
			'cache' => true,
		],
		'id_coment' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'content' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'id' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'user_id' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
	];
}