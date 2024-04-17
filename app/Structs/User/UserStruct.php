<?php

namespace App\Structs\User;

use App\Libs\Serializer\Normalize;
use App\Structs\Struct;
use Illuminate\Support\Carbon;

class UserStruct extends Struct
{
	public ?Carbon $email_verified_at;
	public ?Carbon $created_at;
	public ?Carbon $updated_at;
	public ?int $status;
	public ?string $id;
	public ?string $remember_token;
	public ?string $password;
	public ?string $username;
	public ?string $name;
	public ?string $email;
	public function __construct(object|array $data)
	{
		if (is_object($data)) {
			$data = $data->toArray();
		}

		$this->email_verified_at = Normalize::initCarbon($data, 'email_verified_at');
		$this->created_at = Normalize::initCarbon($data, 'created_at');
		$this->updated_at = Normalize::initCarbon($data, 'updated_at');
		$this->status = Normalize::initInt($data, 'status');
		$this->id = Normalize::initString($data, 'id');
		$this->remember_token = Normalize::initString($data, 'remember_token');
		$this->password = Normalize::initString($data, 'password');
		$this->username = Normalize::initString($data, 'username');
		$this->name = Normalize::initString($data, 'name');
		$this->email = Normalize::initString($data, 'email');

	}
}