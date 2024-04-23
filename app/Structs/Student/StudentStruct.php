<?php

namespace App\Structs\Student;

use App\Libs\Serializer\Normalize;
use App\Structs\Struct;
use Illuminate\Support\Carbon;

class StudentStruct extends Struct
{
	public ?Carbon $created_at;
	public ?Carbon $updated_at;
	public ?Carbon $doB;
	public ?string $name;
	public ?string $gender;
	public ?string $id;
	public ?string $id_helper;
	public ?string $id_parent;
	public ?string $id_class;
	public function __construct(object|array $data)
	{
		if (is_object($data)) {
			$data = $data->toArray();
		}

		$this->created_at = Normalize::initCarbon($data, 'created_at');
		$this->updated_at = Normalize::initCarbon($data, 'updated_at');
		$this->doB = Normalize::initCarbon($data, 'doB');
		$this->name = Normalize::initString($data, 'name');
		$this->gender = Normalize::initString($data, 'gender');
		$this->id = Normalize::initString($data, 'id');
		$this->id_helper = Normalize::initString($data, 'id_helper');
		$this->id_parent = Normalize::initString($data, 'id_parent');
		$this->id_class = Normalize::initString($data, 'id_class');

	}
}