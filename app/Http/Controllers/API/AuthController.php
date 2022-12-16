<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Validator;

class AuthController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','logout','refresh']]);
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6',
            ]);
            
            if($validator->fails()){
                return $this->sendError('Resource Not Found', '', 404);     
            }
            else if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
                $credentials = $request->only('email', 'password');
                $token = Auth::attempt($credentials);
    
                $user = Auth::user();
                $getUser['name'] = $user->name;
                $getUser['email'] = $user->email;
                $getUser['created_at'] = $user->created_at;
    
                $data['user'] = $getUser;
                $data['token'] = $token;
                $message = 'User login successfully';
                return $this->sendResponse($data, $message, Response::HTTP_OK);      
            }
            else{
                return $this->sendError('Resource Not Found', '', 404);
            }
        }
        catch(\RuntimeException $e) 
        {
            return $this->sendError($e->getMessage(), '', 500);
        }
    }

    public function register(Request $request){
        try {
            $user = User::all();
            if($user){
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:6',
                ]);
           
                if($validator->fails()){
                    return $this->sendError('Resource Not Found', '', 404);    
                }
        
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                $getUser['name'] = $user->name;
                $getUser['email'] = $user->email;
                $getUser['created_at'] = $user->created_at;
        
                $token = Auth::login($user);
                $data['user'] = $getUser;
                $data['token'] = $token;
                $message = 'User register successfully';
                return $this->sendResponse($data, $message, Response::HTTP_OK);
            }
            else{
                return $this->sendError('Unauthorized', '', 401);
            }
        }
        catch(\RuntimeException $e) 
        {
            return $this->sendError($e->getMessage(), '', 500);
        }
    }

    public function logout()
    {
        try {
            if(Auth::user()){
                Auth::logout();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully logged out',
                ]);
            }
            else{
                return $this->sendError('Unauthorized', '', 401);
            }
        }
        catch(\RuntimeException $e) 
        {
            return $this->sendError($e->getMessage(), '', 500);
        }
    }

    public function refresh()
    {
        try {
            if(Auth::user()){
                $user = Auth::user();
                $getUser['name'] = $user->name;
                $getUser['email'] = $user->email;
                $getUser['created_at'] = $user->created_at;
                $token = Auth::login($user);
                $data['user'] = $getUser;
                $data['token'] = $token;
                $message = 'Token refresh successfully';
                return $this->sendResponse($data, $message, Response::HTTP_OK);
            }
            else{
                return $this->sendError('Unauthorized', '', 401);
            }
        }
        catch(\RuntimeException $e) 
        {
            return $this->sendError($e->getMessage(), '', 500);
        }
    }

}