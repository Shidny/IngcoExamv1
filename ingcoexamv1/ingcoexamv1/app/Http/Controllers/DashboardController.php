<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\PostLike;



class DashboardController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        // $this->MainController = $Main;
    }

    public function index(Request $request)
    {
        $authData = Auth::user();
        $posts = Post::with(['user', 'comments.user', 'postLikes'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($post) use ($authData) {
                // Check if the current user has liked this post
                $liked = $post->postLikes->contains('user_id', $authData->id);
                
                return [
                    'id' => $post->id,
                    'user_id' => $post->user_id,
                    'author' => $post->user->name,
                    'avatar' => $post->user->profile_image 
                        ? asset('storage/' . $post->user->profile_image) 
                        : '/dist/img/user2-160x160.jpg',
                    'timestamp' => $post->created_at->diffForHumans(),
                    'content' => $post->content,
                    'likes' => $post->likes,
                    'liked' => $liked,
                    'comments' => $post->comments->map(function ($comment) use ($authData) {
                        return [
                            'id' => $comment->id,
                            'user_id' => $comment->user_id,
                            'author' => $comment->user->name,
                            'avatar' => $comment->user->profile_image 
                                ? asset('storage/' . $comment->user->profile_image) 
                                : '/dist/img/user2-160x160.jpg',
                            'text' => $comment->text,
                            'isOwner' => $comment->user_id === $authData->id,
                        ];
                    })->toArray(),
                    'isOwner' => $post->user_id === $authData->id,
                ];
            });

        return Inertia::render('Dashboard', [
            'entities' => [
                'id' => $authData->id,
                'name' => $authData->name,
                'email' => $authData->email,
                'profile_image' => $authData->profile_image 
                    ? asset('storage/' . $authData->profile_image) 
                    : '/dist/img/user2-160x160.jpg',
            ],
            'posts' => $posts,
        ]);
    }
}
