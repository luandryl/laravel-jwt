<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
		*/
		
    public function authenticate (Request $req) {

			$credentials = $req->only('email','password');
			$token = JWTAuth::attempt($credentials);

			try {
				if (!$token) {
					return response()->json([
						'Error' => 'Invalid Credentials'
					], 401);
				}

			} catch (JWTException $e) {
				return response()->json(['error' => 'could_not_create_token'], 500);
			}

			$objectToken = JWTAuth::setToken($token);
			$expiration = JWTAuth::decode($objectToken->getToken())->get('exp');
	
			return response()->json([
				'access_token' => $token,
				'token_type' => 'bearer',
				'expires_in' => $expiration
			]);
			
    }
}
