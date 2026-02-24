<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\PostLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:1|max:5000',
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'likes' => 0,
        ]);

        return redirect()->back()->with('success', 'Post created successfully');
    }

    public function update(Request $request, Post $post)
    {
        // Check if user owns the post
        if ($post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:1|max:5000',
        ]);

        $post->update([
            'content' => $validated['content'],
        ]);
        return redirect()->back()->with('success', 'Post updated successfully');
    }

    public function storeComment(Request $request, Post $post)
    {
        $validated = $request->validate([
            'text' => 'required|string|min:1|max:1000',
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'text' => $validated['text'],
        ]);

        return redirect()->back()->with('success', 'Comment added successfully');
    }

    public function destroy(Post $post)
    {
        // Check if user owns the post
        if ($post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully');
    }

    public function toggleLike(Post $post)
    {
        $userId = Auth::id();
        
        // Check if user already liked the post
        $existingLike = PostLike::where('user_id', $userId)
            ->where('post_id', $post->id)
            ->first();
        
        if ($existingLike) {
            // Unlike the post
            $existingLike->delete();
            $post->decrement('likes');
        } else {
            // Like the post
            PostLike::create([
                'user_id' => $userId,
                'post_id' => $post->id,
            ]);
            $post->increment('likes');
        }
        
        return redirect()->back()->with('success', 'Like updated successfully');
    }

    public function updateComment(Request $request, Comment $comment)
    {
        // Check if user owns the comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'text' => 'required|string|min:1|max:1000',
        ]);

        $comment->update([
            'text' => $validated['text'],
        ]);

        return redirect()->back()->with('success', 'Comment updated successfully');
    }
}
