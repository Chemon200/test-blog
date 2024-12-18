<?php

namespace App\Http\Controllers;

use App\Helper\JwtAuth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request) {
        $userData = json_decode($request->input('data', null), true);

        if (empty($userData)) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado correctamente',
                    'errors' => ['data' => ['Invalid data']]
                ],
                404
            );
        }

        $userData = array_map('trim', $userData);

        $rules = [
            'name' => 'bail|required|regex:/^[\pL\s]+$/u|max:50',
            'surnames' => 'bail|regex:/^[\pL\s]+$/u|max:255',
            'email' => 'bail|required|email|unique:users|max:255|',
            'password' => 'bail|required|max:255'
        ];

        $validate = Validator::make($userData, $rules);

        if ($validate->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado correctamente',
                    'errors' => $validate->errors()
                ],
                404
            );
        }      
        
        $password = hash('sha256', $userData['password']);
        
        $user = new User();
        $user->name = $userData['name'];
        $user->surnames = $userData['surnames'];
        $user->email = $userData['email'];
        $user->password = $password;
        $user->role = 'ROLE_USER';
        
        $user->save();

        return response()->json(
            [
            'status' => 'success',
            'code' => 201,
            'message' => 'El usuario se ha creado correctamente',
            'user' => $user
            ],
            201
        );
    }

    public function login(Request $request) {
        $jwtAuth = new JwtAuth();

        $userData = json_decode($request->input('user_data', null), true);

        if (!$userData) {
            return response()->json(
                [
                'status' => 'error',
                'code' => 403,
                'message' => 'Login incorrecto'
                ],
                403
            );
        }
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $validate = Validator::make($userData, $rules);

        if ($validate->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Los datos no son correctos',
                    'errors' => $validate->errors()
                ],
                404
            );
        } 

        return  response()->json($jwtAuth->signup($userData['email'], hash('sha256', $userData['password'])), 200);
    }

    public function update(Request $request)
    {       
        $userData = json_decode($request->input('data', null), true);

        $rules = [
            'name' => 'bail|required|alpha|max:50',
            'surnames' => 'bail|alpha|max:255',
            'email' => 'bail|required|email|max:255|unique:users,email,'
        ];
        
       
        return response()->json($data, $data['code']);
    }

    public function upload(Request $request)
    {
        $image = $request->file('file0');

        if (!$image) {
            return [
                'status' => 'error',
                'code' => 400,
                'message' => 'Error al subir imagen'
            ];
        }

        $rules = [
            'file0' => 'required|image|mimes:jpg,jpeg,png'
        ];
        $validate = Validator::make($request->all(), $rules);

        if ($validate->fails()) {
            return [
                'status' => 'error',
                'code' => 400,
                'message' => 'El formato de la imagen no es correcto'
            ];
        }
        
        $imageName = time().$image->getClientOriginalName();
        Storage::disk('users')->put($imageName, File::get($image));

        $data = [
            'status' => 'success',
            'code' => 200,
            'image' => $imageName
        ];

        return response()->json($data, $data['code'])->header('Content-Type', 'text/plain');
    }

    public function getImage(string $filename = null)
    {
        if (!$filename || !Storage::disk('users')->exists($filename)) {
            return response()->json(
                [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'La imagen indicada no existe'
                ],
                404
            );
        }
        $file = Storage::disk('users')->get($filename);

        return new Response($file, 200);
    }

    public function profile($userId = null)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(
                [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'Usuario no encontrado'
                ],
                400
            );
        }

        return response()->json(
            [
                'code'=> 200,
                'status' => 'success',
                'user' => $user
            ],
            400
        );
    }
}
