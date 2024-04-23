<?php

namespace App\Consts\Schema;
use App\Consts\DbTypes;

abstract class DBCommentFields
{
	const COMMENT = [
		'status' => [
			'type' => DbTypes::INT,
			'cache' => true,
		],
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
		'content' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'id_ref' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'id_post' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
	];
}