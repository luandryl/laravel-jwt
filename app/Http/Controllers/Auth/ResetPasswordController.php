<?php

namespace App\Http\Controllers\Auth;

use Hash;
use JWTAuth;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\ResetsPasswords;


class ResetPasswordController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	public function resetPassword(Request $req) {

		if (!$this->validator($req->all())->fails()) {

			$data = $req->all();

			$user = User::where('email', '=', $req->get('email'))->first();

			$user->password = bcrypt($data['new_password']);
		
			if (!$user->save()) {
				return response()->json([
					'error' => 'Could not save the User'
				], 500);
			}
			
			$token = JWTAuth::getToken();
			
			$new_token = JWTAuth::refresh($token);
			$objectToken = JWTAuth::setToken($new_token);
			$expiration = JWTAuth::decode($objectToken->getToken())->get('exp');
	
			return response()->json([
				'access_token' => $new_token,
				'token_type' => 'bearer',
				'expires_in' => $expiration
			]);

		} 

		return response()->json([
			'message'   => 'Validation Failed',
			'errors'    => $this->validator($req->all())->errors()->all()
		], 422);
	
			
	}

	/**
		* Get a validator for an incoming registration request.
		*
		* @param  array  $data
		* @return \Illuminate\Contracts\Validation\Validator
		*/
		protected function validator(array $data) {
			return Validator::make($data, [
				'email' => 'required|string|email|max:255',
				'password' => 'required|string|min:2',
				'new_password' => 'required|string|min:6|different:password',
				'new_password_confirmation' => 'required|string|min:6|same:new_password'
			]);
		}

}
