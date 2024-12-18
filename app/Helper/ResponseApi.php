<?php
namespace App\Helper;

use Illuminate\Http\Response;

class ResponseApi
{
    public function jsonSuccess(int $code, $content)
    {
        return response()->json(
            [
                'code' => $code,
                'status' => 'success',
                'content' => $content
            ],
            $code
        );
    }

    public function jsonError(int $code, array $errors)
    {
        return response()->json(
            [
                'code' => $code,
                'status' => 'error',
                'errors' => $errors
            ],
            $code
        );
    }
}