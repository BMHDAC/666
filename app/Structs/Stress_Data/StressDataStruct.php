<?php

namespace App\Structs\Stress_Data;

use Illuminate\Support\Carbon;
use App\Structs\Struct;
use App\Libs\Serializer\Normalize;

class StressDataStruct extends Struct
{
    public ?string $id;
    public ?string $user_id;
    public ?string $device_id;
    public ?Carbon $datetime;
    public ?int $stress_level;
    public ?float $average_heart_rate;
    public ?float $latitude;
    public ?float $longitude;
    public ?string $prediction;

    public function __construct(object|array $data)
    {

        if (is_object($data)) {
            $data = $data->toArray();
        }
        $this->id = Normalize::initString($data, 'id');
        $this->user_id = Normalize::initString($data, 'user_id');
        $this->device_id = Normalize::initString($data, 'device_id');
        $this->datetime = Normalize::initCarbon($data, 'datetime');
        $this->stress_level = Normalize::initInt($data, 'stress_level');
        $this->average_heart_rate = Normalize::initFloat($data, 'average_heart_rate');
        $this->latitude = Normalize::initFloat($data, 'latitude');
        $this->longitude = Normalize::initFloat($data, 'longitude');
        $this->prediction = Normalize::initString($data, 'prediction');
    }
}
