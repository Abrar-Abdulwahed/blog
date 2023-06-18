<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        $success = $this->userData($user);
   
        return $this->sendResponse($success, 'User register successfully.');
    }


    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success = $this->userData($user);
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['Unauthorised User']);
        } 
    }
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
    }

    public function userData(User $user){
        return array(
            'token'=> $user->createToken('blogToken')->plainTextToken,
            'type'=> 'bearer',
            'name'=> $user->name,
            'email'=>$user->email,
            'bod'=>$user->bod,
        );
    }
}
