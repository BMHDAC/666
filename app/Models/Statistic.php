<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Statistic extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        "user_id",
        "step_count",
        "stair_step_count",
        "heart_rate",
        "distance",
    ];

    public static function get_all(): Collection
    {
        return DB::table("statistics")
            ->select(["*"])
            ->distinct()
            ->orderBy("datetime", "desc")
            ->get();
    }
    public static function get_avg_of_user(string $user_id, DateTime $datetime): Collection
    {
        return DB::table("statistics")
            ->selectRaw("AVG(heart_rate) as avg_heart_rate, AVG(step_count) as avg_step_count, AVG(stair_step_count) as avg_stair_steps, AVG(distance) as avg_distance")
            ->where("user_id", $user_id)
            ->whereDate("datetime", $datetime)
            ->get();
    }
}
