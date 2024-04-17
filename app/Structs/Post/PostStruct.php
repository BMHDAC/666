<?php

namespace App\Structs\Post;

use App\Libs\Serializer\Normalize;
use App\Structs\Struct;
use Illuminate\Support\Carbon;

class PostStruct extends Struct
{
	public ?Carbon $created_at;
	public ?Carbon $updated_at;
	public ?string $id;
	public ?string $name;
	public ?string $description;
	public function __construct(object|array $data)
	{
		if (is_object($data)) {
			$data = $data->toArray();
		}

		$this->created_at = Normalize::initCarbon($data, 'created_at');
		$this->updated_at = Normalize::initCarbon($data, 'updated_at');
		$this->id = Normalize::initString($data, 'id');
		$this->name = Normalize::initString($data, 'name');
		$this->description = Normalize::initString($data, 'description');

	}
}