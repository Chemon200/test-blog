<?php

declare( strict_types = 1 );

namespace App\Helper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use App\User\Domain\UserRole;

Class JwtAuth {
    const KEY_TOKEN = 'dfkjg34u598y34v893498v5349vm934y5v8934ymv5';

    public function signup(string $email, string $password, $getToken = null)
    {
        $user = User::where(
            [
            'email' => $email,
            'password' => $password
            ]
        )->first();

        $signup = false;

        if (is_object($user)) {
            $signup = true;
        }

        if (!$signup) {
            return [
                'status' => 'error',
                'message' => 'login incorrecto'
            ];
        }
        
        $token = [
            'sub' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'surname' => $user->surnames,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60)
        ];

        $jwt = JWT::encode($token, self::KEY_TOKEN, 'HS256');
        
        if (null === $getToken) {
            $data = $jwt;
        } else {
            $decoded = JWT::decode($jwt, new Key(self::KEY_TOKEN, 'HS256'));
            $data = $decoded;
        }

        return $data;
    }

    public function checkToken(string $jwt, bool $getEntity = false)
    {
        $auth = false;

        try {
            $tokenDecoded = JWT::decode($jwt, new Key(self::KEY_TOKEN, 'HS256'));
        } catch (\UnexpectedValueException $exception) {
            $auth = false;
        } catch (\DomainException $exception) {
            $auth = false;
        }

        if (!empty($tokenDecoded) && is_object($tokenDecoded) && isset($tokenDecoded->sub)) {
            $auth = true;
        }

        if ($auth && $getEntity) {
            return $tokenDecoded;
        }

        return $auth;
    }

    public function checkAdminToken(string $jwt): bool
    {
        try {
            $userData = JWT::decode($jwt, new Key(self::KEY_TOKEN, 'HS256'));
        } catch (\UnexpectedValueException $exception) {
            return false;
        } catch (\DomainException $exception) {
            return false;
        }

        if (UserRole::ROLE_ADMIN !== $userData->role) {
            return false;
        }

        return true;
    }

    public function getUserInformation(string $token)
    {
        return $this->checkToken($token, true);
    }
}

