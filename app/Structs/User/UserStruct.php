<?php

namespace App\Structs\User;

use App\Consts\DBRole;
use App\Libs\Serializer\Normalize;
use App\Structs\Struct;
use Illuminate\Support\Carbon;

class UserStruct extends Struct
{
	public ?Carbon $created_at;
	public ?Carbon $updated_at;
	public ?int $status;
	public ?int $role;
	public ?Carbon $email_verified_at;
	public ?string $remember_token;
	public ?string $id;
	public ?string $image;
	public ?string $username;
	public ?string $name;
	public ?string $email;
	public ?string $password;
	public function __construct(object|array $data)
	{
		if (is_object($data)) {
			$data = $data->toArray();
		}

		$this->created_at = Normalize::initCarbon($data, 'created_at');
		$this->updated_at = Normalize::initCarbon($data, 'updated_at');
		$this->status = Normalize::initInt($data, 'status');
		$this->role = Normalize::initInt($data, 'role');
		$this->email_verified_at = Normalize::initCarbon($data, 'email_verified_at');
		$this->remember_token = Normalize::initString($data, 'remember_token');
		$this->id = Normalize::initString($data, 'id');
		$this->image = Normalize::initString($data, 'image');
		$this->username = Normalize::initString($data, 'username');
		$this->name = Normalize::initString($data, 'name');
		$this->email = Normalize::initString($data, 'email');
		$this->password = Normalize::initString($data, 'password');

	}
    public function getRole(){

        switch ($this->role){
            case DBRole::HELPER : return "helper";
            case DBRole::TEACHER : return "teacher";
            case DBRole::PARENT : return "parent";
        }
    }
}
