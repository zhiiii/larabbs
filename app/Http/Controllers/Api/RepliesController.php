<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ReplyRequest;
use App\Models\Reply;
use App\Models\Topic;
use App\Transformers\ReplyTransformer;

class RepliesController extends Controller
{
	public function store(ReplyRequest $request, $topicId, Reply $reply)
	{
		$reply->content = $request->content;
		$reply->topic_id = $topicId;
		$reply->user_id = $this->user()->id;
		$reply->save();

		return $this->response->item($reply, new ReplyTransformer())
			->setStatusCode(201);
	}

	public function destroy(Topic $topic, Reply $reply)
	{
		if ($reply->topic_id != $topic->id){
			return $this->response->errorBadRequest();
		}

		$this->authorize('destroy', $reply);
		$reply->delete();

		return $this->response->noContent();
	}
}
