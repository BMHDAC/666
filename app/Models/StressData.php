<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Structs\Stress_Data\StressDataStruct;
use DateTime;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StressData extends Model
{
    use HasUlids, HasFactory;

    public $timestamps = false;
    protected $fillable = [
        "id",
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

        return self::query()->insertGetId($data);
    }

    public static function get_all($select = ['*']): ?object
    {

        return self::query()
            ->select($select)
            ->distinct()
            ->orderBy('datetime', 'desc')
            ->get();
    }

    public static function get_by_user_id(string $id, $select = ['*']): ?object
    {
        if (!Str::isUlid($id)) {
            return null;
        };

        return self::query()
            ->select($select)
            ->where('user_id', $id)
            ->distinct()
            ->orderBy('datetime', 'desc')
            ->get();
    }

    // Get all row entry by user id and datetime

    public static function get_by_user_id_at_date(string $user_id, DateTime $datetime): ?object
    {
        if (!Str::isUlid($user_id)) {
            return null;
        };
        $datetime = $datetime->format('Y-m-d H:i:s');
        if (!DateTime::createFromFormat('Y-m-d H:i:s', $datetime)) {
            return null;
        }
        return DB::table('stress_data')
            ->select('*')
            ->where('user_id', $user_id)
            ->whereDate('datetime', $datetime)
            ->get();
    }
    public static function get_analyzed_by_user_id(string $user_id, DateTime $datetime): ?object
    {
        if (!Str::isUlid($user_id)) {
            return null;
        };
        return DB::table('stress_data')
            ->selectRaw('AVG(average_heart_rate) as average_heart_rate, AVG(step_count) as step_count')
            ->where('user_id', $user_id)
            ->whereDate('datetime', $datetime)
            ->get();
    }

    public static function get_entry_by_id(string $id)
    {
        if (Str::isUuid($id) || Str::isUlid($id)) {
            return self::query()
                ->where('id', $id)
                ->first();
        } else {
            return null;
        };
    }

    public static function delete_entry_by_id(string $id)
    {
        return self::query()->where('id', $id)->delete();
    }
}
