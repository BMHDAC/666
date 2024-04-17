<?php

namespace App\Consts\Schema;
use App\Consts\DbTypes;

abstract class DBUserFields
{
	const USERS = [
		'email_verified_at' => [
			'type' => DbTypes::STRING,
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
		'status' => [
			'type' => DbTypes::INT,
			'cache' => true,
		],
		'id' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'remember_token' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'password' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'username' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'name' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
		'email' => [
			'type' => DbTypes::STRING,
			'cache' => true,
		],
	];
}