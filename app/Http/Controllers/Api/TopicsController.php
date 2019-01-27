<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TopicRequest;
use App\Models\Topic;
use App\Transformers\TopicTransformer;

class TopicsController extends Controller
{
    public function store(TopicRequest $request, Topic $topic)
    {
		$topic->fill($request->all());
		$topic->user_id = $this->user()->id;
		$topic->save();

		return $this->response->item($topic, new TopicTransformer())->setStatusCode(201);
    }

    public function destroy(Topic $topic)
    {
    	$this->authorize('destroy', $topic);

    	$topic->delete();
    	return $this->response->noContent();
    }
}
