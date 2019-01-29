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
	'middleware' => ['bindings'],
], function ($api) {

	// 未登录
	$api->group([
		// 节流限制设定
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

	// 登录成功
	$api->group([
		// 节流限制设定
		'middleware' => ['api.throttle'],
		'limit' => config('api.rate_limits.access.limit'),
		'expires' => config('api.rate_limits.access.expires')
	], function ($api) {
		// 游客可以访问的接口
		// 分类列表
		$api->get('categories', 'CategoriesController@index')->name('api.categories.index');
		// 帖子列表
		$api->get('topics', 'TopicsController@index')->name('api.topics.index');
		// 用户发表的帖子
		$api->get('users/{user}/topics', 'TopicsController@userIndex')->name('api.users.topics.index');
		// 帖子详情
		$api->get('topics/{topic}', 'TopicsController@show')->name('api.topics.show');

		// 需要token验证的接口
		$api->group([
			'middleware' => ['api.auth'],
		], function ($api) {
			// 当前登录用户信息
			$api->get('user', 'UsersController@me')->name('api.user.show');
			// 编辑登录用户信息
			$api->patch('user', 'UsersController@update')->name('api.user.update');
			// 图片资源
			$api->post('images', 'ImagesController@store')->name('api.images.store');
			// 发布话题
			$api->post('topics', 'TopicsController@store')->name('api.topics.store');
			// 编辑话题
			$api->patch('topics/{topic}', 'TopicsController@update')->name('api.topics.update');
			// 删除话题
			$api->delete('topics/{topic}', 'TopicsController@destroy')->name('api.topics.destroy');
			// 回复帖子
			$api->post('topics/{topic}/replies', 'RepliesController@store')->name('api.topics.replies.store');
			// 删除回复

			//
		});
	});
});
