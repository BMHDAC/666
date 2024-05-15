<?php

namespace App\Consts\Schema;

use App\Consts\DbTypes;

abstract class DBStressDataFields
{
    const STRESS_DATA = [
        'id' => [
            'type' => DbTypes::STRING,
            'cache' => true,
        ],
        "stress_level" => [
            'type' => DbTypes::INT,
            'cache' => true,
        ],
        "datetime" => [
            'type' => DbTypes::STRING,
            'cache' => true,
        ],
        "user_id" => [
            'type' => DbTypes::STRING,
            'cache' => true,
        ],
        "average_heart_rate" => [
            'type' => DbTypes::FLOAT,
            'cache' => true,
        ],
        "latitude" => [
            'type' => DbTypes::FLOAT,
            'cache' => true,
        ],
        "longitude" => [
            'type' => DbTypes::FLOAT,
            'cache' => true,
        ],
        "step_count" => [
            'type' => DbTypes::INT,
            'cache' => true,
        ],
        "device_id" => [
            'type' => DbTypes::STRING,
            'cache' => true,
        ]
    ];
}
