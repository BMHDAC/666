<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Structs\Stress_Data\StressDataStruct;
use DateTime;
use Illuminate\Support\Str;

class Stress_Data extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stress_level',
        'datetime',
        'prediction',
        'average_heart_rate',
        'latitude',
        'longitude',
        'device_id'
    ];

    public function struct(): StressDataStruct
    {
        return new StressDataStruct($this->getAttributes());
    }

    public static function add_stress_data(array $data)
    {

        return self::query()->insert($data);
    }

    public static function get_all($select = ['*'], array $opts = []): ?object
    {

        return self::query()
            ->select($select)
            ->distinct()
            ->first($opts);
    }

    public static function get_by_user_id(string $id, $select = ['*'], array $opts = []): ?object
    {
        if (!Str::isUlid($id)) {
            return null;
        };

        return self::query()
            ->select($select)
            ->where('user_id', $id)
            ->get();
    }

    // Get all row entry by user id and datetime

    public static function get_by_user_id_at_date(string $user_id, DateTime $datetime, $select = ['*'], array $opts = []): ?object
    {
        if (!Str::isUlid($user_id)) {
            return null;
        };
        $datetime = $datetime->format('Y-m-d H:i:s');
        if (!DateTime::createFromFormat('Y-m-d H:i:s', $datetime)) {
            return null;
        }
        return self::query()
            ->select($select)
            ->where('user_id', $user_id)
            ->whereDate('datetime', $datetime)
            ->get();
    }
}
