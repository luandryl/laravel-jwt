<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use JWTAuth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function singup (Request $req) {

			if (!$this->validator($req->all())->fails()) {
				$user = $this->create($req->all());

				if (!$user->save()) {
					return response()->json([
							'error' => 'Could not save the User'
					], 500);
				}

				$token = JWTAuth::fromUser($user);
				
				$objectToken = JWTAuth::setToken($token);
				$expiration = JWTAuth::decode($objectToken->getToken())->get('exp');
		
				return response()->json([
					'access_token' => $token,
					'token_type' => 'bearer',
					'expires_in' => $expiration
				]);

			} 

			return response()->json([
				'message'   => 'Validation Failed',
				'errors'    => $this->validator($req->all())->errors()->all()
			], 422);
			
    }
}
