<?php

declare( strict_types = 1 );

namespace App\User\Domain;

use Exception;
use Illuminate\Support\Facades\Validator;

class UserRole
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLES = [self::ROLE_ADMIN, 'ROLE_USER'];
    protected string $name;

    public function getName():string
    {
        return $this->name;
    }

    public function setName($name): string
    {
        if (!in_array($name, self::ROLES)) {
            new Exception('The indicated role is not valid');
        }

        $this->name = $name;

        return $this->getName();
    }
}