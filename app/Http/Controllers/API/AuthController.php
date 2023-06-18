<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Validation\Rule;

class AuthController extends BaseController
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required',
            'password'  => 'required',
            'confirm_password' => 'required|same:password',
            'bod'       => 'required|date|before:-18 years',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('blogToken')->plainTextToken;
        $success['type'] =  'bearer';
        $success['name'] = $user->name;
        $success['email'] = $user->email;
        $success['password'] = $input['password'];
        $success['bod'] = $user->bod;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
}
