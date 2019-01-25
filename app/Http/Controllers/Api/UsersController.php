<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Transformers\UserTransformer;

class UsersController extends Controller
{
	public function store(UserRequest $request)
	{
		$verifyData = \Cache::get($request->verification_key);

		if (!$verifyData) {
			return $this->response->error('验证码已失效', 422);
		}

		if (!hash_equals($verifyData['code'], $request->verification_code)) {
			return $this->response->erroUnauthorized('验证码有误');
		}

		$user = User::create([
			'name' => $request->name,
			'phone' => $verifyData['phone'],
			'email' => $request->email,
			'password' => bcrypt($request->password)
		]);

		\Cache::forget($request->verification_key);
//		return $this->response->created();
		return $this->response->item($user, new UserTransformer())
			->setMeta([
				'access_token' => \Auth::guard('api')->fromUser($user),
				'token_type' => 'Bearer',
				'expires_in' => \Auth::guard('api')->factory()->getTTL() * 60
			])->setStatusCode(201);
	}

	public function me(UserTransformer $userTransformer)
	{
		return $this->response->item($this->user(), $userTransformer);
	}
}
