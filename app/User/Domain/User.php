<?php

declare( strict_types = 1 );

namespace App\User\Domain;

use App\User\Domain\UserRole;
use Exception;

class UserModel
{
    public int $id;
    public string $name;
    public string $surnames;
    public string $email;
    public string $password;
    public UserRole $role;

    public function setRole(string $role)
    {
        return $this->role->setName($role);
    }

    public function getRole()
    {
        return $this->role->getName();
    }
}