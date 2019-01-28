<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Request;
use App\Http\Requests\TopicRequest;
use App\Models\Topic;
use App\Models\User;
use App\Transformers\TopicTransformer;

class TopicsController extends Controller
{
	/**
	 * 列表
	 * @param TopicRequest $request
	 * @param Topic $topic
	 * @return \Dingo\Api\Http\Response
	 */
	public function index(TopicRequest $request, Topic $topic)
	{
		$query = $topic->query();

		if ($categoryId = $request->category_id) {
			$query->where('cateogory_id', $category_id);
		}
		switch ($request->order) {
			case "recent" :
				$query->recent();
				break;

			default:
				$query->recentReplied();
				break;
		}

		$topics = $query->paginate(5);

		return $this->response->paginator($topics, new TopicTransformer());

	}

	/**
	 * 用户的帖子列表
	 * @param User $user
	 * @return \Dingo\Api\Http\Response
	 */
	public function userIndex(User $user)
	{
		$topics = $user->topics()->recent()->paginate(5);
		return $this->response->paginator($topics, new TopicTransformer());
	}

	/**
	 * 帖子展示
	 * @param Topic $topic
	 * @return \Dingo\Api\Http\Response
	 */
	public function show(Topic $topic)
	{
		return $this->response->item($topic, new TopicTransformer());
	}

	/**
	 * 保存
	 * @param TopicRequest $request
	 * @param Topic $topic
	 * @return \Dingo\Api\Http\Response
	 */
    public function store(TopicRequest $request, Topic $topic)
    {
		$topic->fill($request->all());
		$topic->user_id = $this->user()->id;
		$topic->save();

		return $this->response->item($topic, new TopicTransformer())->setStatusCode(201);
    }

	/**
	 * 删除
	 * @param Topic $topic
	 * @return \Dingo\Api\Http\Response
	 * @throws \Illuminate\Auth\Access\AuthorizationException
	 */
    public function destroy(Topic $topic)
    {
    	$this->authorize('destroy', $topic);

    	$topic->delete();
    	return $this->response->noContent();
    }
}
