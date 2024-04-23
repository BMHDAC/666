<?php

namespace App\Consts\Schema;
use App\Consts\DbTypes;

abstract class DBStudentFields
{
	const STUDENT = [
		'created_at' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'updated_at' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'doB' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'name' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'gender' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'id' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'id_helper' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'id_parent' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'id_class' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
	];
}