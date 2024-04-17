<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use PhpParser\Builder;

class PostModel extends Model
{
    use HasUlids, HasFactory;
    protected $table = 'post';

    public static function doAdd($data):bool
    {
        return self::query()->insert($data);
    }
    public static function doGet(array $filter): LengthAwarePaginator|Collection
    {
        $query = self::query()
            ->orderBy($filter['sort_by'], $filter['sort'])
            ->where(function ($query) use ($filter) {
                if ($filter['search_by']) {
                    $query->where($filter['search_by'], 'LIKE', $filter['key']);
                }
            });

        if (empty($filter['limit'])) {
            return $query->get($filter['fields']);
        } else {

            return $query->paginate($filter['limit'], $filter['fields'], "{$filter['page']}", $filter['page']);
        }
    }
}
