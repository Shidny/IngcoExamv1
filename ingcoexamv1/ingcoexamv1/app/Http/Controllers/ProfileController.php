<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use App\Models\Post;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch user's posts with relationships
        $posts = Post::with(['user', 'comments.user', 'postLikes'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($post) use ($user) {
                $liked = $post->postLikes->contains('user_id', $user->id);
                
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
                    'comments' => $post->comments->map(function ($comment) use ($user) {
                        return [
                            'id' => $comment->id,
                            'user_id' => $comment->user_id,
                            'author' => $comment->user->name,
                            'avatar' => $comment->user->profile_image 
                                ? asset('storage/' . $comment->user->profile_image) 
                                : '/dist/img/user2-160x160.jpg',
                            'text' => $comment->text,
                            'isOwner' => $comment->user_id === $user->id,
                        ];
                    })->toArray(),
                    'isOwner' => true,
                ];
            });
        
        return Inertia::render('Profile', [
            'entities' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : '/dist/img/user2-160x160.jpg',
                'age' => $user->age,
                'address' => $user->address,
                'contact_number' => $user->contact_number,
            ],
            'posts' => $posts,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:1|max:150',
            'address' => 'nullable|string|max:500',
            'contact_number' => 'nullable|string|max:20',
            'current_password' => 'nullable|string',
            'password' => ['nullable', 'string', Password::min(8), 'confirmed'],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update name and profile fields
        $user->name = $validated['name'];
        $user->age = $validated['age'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->contact_number = $validated['contact_number'] ?? null;

        // Update password if provided
        if ($request->filled('password')) {
            // Verify current password
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }
            
            $user->password = Hash::make($validated['password']);
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Store new profile image
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully');
    }
}
