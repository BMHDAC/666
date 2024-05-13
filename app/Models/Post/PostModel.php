<?php

namespace App\Models\Post;

use App\Structs\Post\PostStruct;
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
    public static function doGetById(string $id, array $filter, ?string $ref = null): Model|Builder|null
    {
        return self::query()
            ->where(function ($query) use ($id, $ref) {
                $query->where('id', $id);
                if ($ref) {
                    $query->where('user_id', $ref);
                }
            })
            ->distinct()
            ->first($filter);
    }
    public static function doEdit(array $data, PostModel $post): bool
    {
        $post->forceFill($data);

        return $post->save();

    }
    public function struct(): PostStruct {
        return new PostStruct ($this->getAttributes());
    }

}

