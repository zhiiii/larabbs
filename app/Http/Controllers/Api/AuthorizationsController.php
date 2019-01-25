<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;

class AuthorizationsController extends Controller
{
	/**
	 * 登录
	 * @param AuthorizationRequest $request
	 */
	public function store(AuthorizationRequest $request)
	{
		$username = $request->username;

		filter_var($username, FILTER_VALIDATE_EMAIL) ?
			$credentials['email'] = $username :
			$credentials['phone'] = $username;

		$credentials['password'] = $request->password;

		if (! $token =  Auth::guard('api')->attempt($credentials)) {
			return $this->response->errorUnauthorized('用户或密码错误');
		}

		return $this->responseWithToken($token)->setStatusCode(201);
	}

	/**
	 * 第三方登录
	 * @param $type
	 * @param SocialAuthorizationRequest $request
	 */
	public function socialStore($type, SocialAuthorizationRequest $request)
	{
		if (!in_array($type, ['weixin'])) {
			return $this->response->errorBadRequest();
		}

		$driver = \Socialite::driver($type);

		try {
			if ($code = $request->code) {
				$response = $driver->getAccessTokenResponse($code);
				$token = array_get($response, 'access_token');
			} else {
				$token = $request->access_token;
				if ($type == 'weixin') {
					$driver->setOpenId($request->openid);
				}
			}

			$oauthUser = $driver->userFromToken($token);
		} catch (\Exception $e) {
			return $this->response->errorUnauthorized();
		}

		switch ($type) {
			case 'weixin' :
				$unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

				if ($unionid) {
					$user = User::where('w_unionid', $unionid)->first();
				} else {
					$user = User::where('w_openid', $oauthUser->getId())->first();
				}

				if (!$user) {
					$user = User::create([
						'name' => $oauthUser->getNickname(),
						'avater' => $oauthUser->getAvatar(),
						'w_openid' => $oauthUser->getId(),
						'w_unionid' => $unionid
					]);
				}
				break;
		}

		$token = Auth::guard('api')->fromUser($user);
		return $this->responseWithToken($token)->setStatusCode(201);
	}

	/**
	 * 返回响应数据
	 * @param $token
	 * @return mixed
	 */
	protected function responseWithToken($token)
	{
		return $this->response->array([
			'access_token' => $token,
			'token_type' => 'Bearer',
			'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
		]);
	}

	/**
	 * 刷新token
	 * @return mixed
	 */
	public function update()
	{
		$token = Auth::guard('api')->refresh();
		return $this->responseWithToken($token);
	}

	/**
	 * 删除token
	 * @return \Dingo\Api\Http\Response
	 */
	public function destory()
	{
		Auth::guard('api')->logout();
		return $this->response->noContent();
	}



}
