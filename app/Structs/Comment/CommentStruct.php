<?php

namespace App\Structs\Comment;

use App\Libs\Serializer\Normalize;
use App\Structs\Struct;
use Illuminate\Support\Carbon;

class CommentStruct extends Struct
{
	public ?int $status;
	public ?Carbon $created_at;
	public ?Carbon $updated_at;
	public ?string $id;
	public ?string $content;
	public ?string $id_ref;
	public ?string $id_post;
	public function __construct(object|array $data)
	{
		if (is_object($data)) {
			$data = $data->toArray();
		}

		$this->status = Normalize::initInt($data, 'status');
		$this->created_at = Normalize::initCarbon($data, 'created_at');
		$this->updated_at = Normalize::initCarbon($data, 'updated_at');
		$this->id = Normalize::initString($data, 'id');
		$this->content = Normalize::initString($data, 'content');
		$this->id_ref = Normalize::initString($data, 'id_ref');
		$this->id_post = Normalize::initString($data, 'id_post');

	}
}