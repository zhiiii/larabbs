<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
], function ($api) {

	$api->group([
		'middleware' => ['api.throttle'],
		'limit' => config('api.rate_limits.sign.limit'),
		'expires' => config('api.rate_limits.sign.expires')
	], function ($api) {
		// 短信验证
		$api->post('verificationCodes', 'VerificationCodesController@store')->name('api.verificationCodes.store');
		// 用户注册
		$api->post('users', 'UsersController@store');
		// 验证码
		$api->post('captchas', 'CaptchasController@store');
		// 第三方登录
		$api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore');
		// 登录
		$api->post('authorizations', 'AuthorizationsController@store');
		// 刷新token
		$api->put('authorizations/current', 'AuthorizationsController@update');
		// 删除token
		$api->delete('authorizations/current', 'AuthorizationsController@destory');
	});

	$api->group([
		'middleware' => ['api.throttle'],
		'limit' => config('api.rate_limits.access.limit'),
		'expires' => config('api.rate_limits.access.expires')
	], function ($api) {
		// 游客可以访问的接口

		// 需要token验证的接口
		$api->group([
			'middleware' => ['api.auth'],
		], function ($api) {
			// 当前登录用户信息
			$api->get('user', 'UsersController@me')->name('api.user.show');
		});
	});
});
