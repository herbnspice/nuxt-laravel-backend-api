<?php

namespace App\Http\Controllers;

Use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Post;
use App\Models\User;
use App\Http\Resources\Topic as TopicResource; 
use App\Http\Requests\TopicCreateRequest; 
use App\Http\Requests\UpdateTopicRequest; 


class TopicController extends Controller
{
    //
    public function index(){
        $topics=Topic::latestFirst()->paginate(5);
        return TopicResource::collection($topics);
    }

    public function update( TopicResource $request, Topic $topic){

        $this->authorize('update', $topic);

        $topic->title =  $request->get('title', $topic->title);
        $topic->save();

        return new TopicResource( $topic );
    }
    public function show( Topic $topic){
        return new TopicResource($topic);

    }
    public function store( TopicCreateRequest $request ){

        $topic = new Topic;
        $topic->title = $request->title;
        $topic->user()->associate($request->user());

        $post = new Post;
        $post->body = $request->body;
        $post->user()->associate($request->user());

        $topic->save();
        $topic->posts()->save($post);

        return new TopicResource($topic);
    }
    public function destroy( User $user, Topic $topic){

        $this->authorize('destroy', $topic);
        $topic->delete();
        return response( null, 204);
    }
}
