<?php

declare( strict_types = 1 );

namespace App\User\Infraestructure;

use App\Helper\ResponseApi;
use App\Helper\ResponseException;
use App\User\Application\ValidateUserDataRegistration;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegisterController extends BaseController
{
    protected ResponseApi $responseApi;
    protected ValidateUserDataRegistration $validateUserDataRegistration;

    public function __construct()
    {
        $this->responseApi = new ResponseApi();
        $this->validateUserDataRegistration = new ValidateUserDataRegistration();
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $this->validateUserDataRegistration->validate($request->input('data', null));
        } catch (ResponseException $exception) {
            return $this->responseApi->jsonError(400, $exception->getDecodedMessage(true));
        } catch (\Exception $exception) {
            return $this->responseApi->jsonError(400, ['errors' => ['An error occurred. '. $exception->getMessage()]]);
        }

        return $this->responseApi->jsonSuccess(201, ['message' => 'El usuario se ha creado correctamente']);
    }
}