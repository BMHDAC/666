<?php

namespace App\Structs\Class;

use App\Libs\Serializer\Normalize;
use App\Structs\Struct;
use Illuminate\Support\Carbon;

class ClassStruct extends Struct
{
	public ?Carbon $created_at;
	public ?Carbon $updated_at;
	public ?string $id;
	public ?string $class;
	public ?string $id_teacher;
	public function __construct(object|array $data)
	{
		if (is_object($data)) {
			$data = $data->toArray();
		}

		$this->created_at = Normalize::initCarbon($data, 'created_at');
		$this->updated_at = Normalize::initCarbon($data, 'updated_at');
		$this->id = Normalize::initString($data, 'id');
		$this->class = Normalize::initString($data, 'class');
		$this->id_teacher = Normalize::initString($data, 'id_teacher');

	}
}