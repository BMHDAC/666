<?php

namespace App\Models;

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

    public static function get_all(): Collection {
        return DB::table("statistics")->get();
    }
}
