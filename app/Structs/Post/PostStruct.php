<?php

namespace App\Structs\Post;

use App\Libs\Serializer\Normalize;
use App\Structs\Struct;
use Illuminate\Support\Carbon;

class PostStruct extends Struct
{
	public ?Carbon $created_at;
	public ?Carbon $updated_at;
	public ?object $media;
	public ?int $status;
	public ?string $id_coment;
	public ?string $content;
	public ?string $id;
	public ?string $user_id;
	public function __construct(object|array $data)
	{
		if (is_object($data)) {
			$data = $data->toArray();
		}

		$this->created_at = Normalize::initCarbon($data, 'created_at');
		$this->updated_at = Normalize::initCarbon($data, 'updated_at');
		$this->media = Normalize::initObject($data, 'media');
		$this->status = Normalize::initInt($data, 'status');
		$this->id_coment = Normalize::initString($data, 'id_coment');
		$this->content = Normalize::initString($data, 'content');
		$this->id = Normalize::initString($data, 'id');
		$this->user_id = Normalize::initString($data, 'user_id');

	}
}