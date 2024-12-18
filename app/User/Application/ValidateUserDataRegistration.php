<?php

namespace App\User\Application;

use App\Helper\ResponseApi;
use App\Helper\ResponseException;
use Illuminate\Support\Facades\Validator;

class ValidateUserDataRegistration
{
    protected array $rules = [
        'name' => 'bail|required|regex:/^[\pL\s]+$/u|max:50',
        'surnames' => 'bail|regex:/^[\pL\s]+$/u|max:255',
        'email' => 'bail|required|email|unique:users|max:255|',
        'password' => 'bail|required|max:255'
    ];

    protected ResponseApi $responseApi;
    
    public function __construct()
    {
        $this->responseApi = new ResponseApi();
    }

    public function validate(?string $userData)
    {
        $decodedUserData = json_decode($userData, true);

        if (empty($decodedUserData)) {
            Throw new ResponseException(['errors' => ['Los datos de usuario no son correctos']]);
        }

        $validate = Validator::make($decodedUserData, $this->rules);

        if ($validate->fails()) {
            Throw new ResponseException(['errors' => $validate->errors()]);
        }
    }
}
