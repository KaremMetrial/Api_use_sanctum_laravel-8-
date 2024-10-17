<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Http\Resources\PostResource;
    use App\Models\Post;
    use Illuminate\Http\Request;

    class PostController extends Controller
    {
        public function index()
        {
            $posts = Post::where('user_id', auth()->id())
                ->with('tags')
                ->orderBy('pinned', 'desc')
                ->get();
            return PostResource::collection($posts);
        }

        public function show(Post $post)
        {
            if ($post->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return new PostResource($post->load('tags'));
        }

        public function update(Request $request, Post $post)
        {
            if ($post->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'body' => 'sometimes|required|string',
                'cover_image' => 'sometimes|image',
                'pinned' => 'required|boolean',
                'tags' => 'sometimes|required|array',
            ]);

            if ($request->hasFile('cover_image')) {
                $validated['cover_image'] = $request->file('cover_image')->store('images');
            }

            $post->update($validated);

            if ($request->tags) {
                $post->tags()->sync($request->tags);
            }

            return new PostResource($post);
        }
        public function store(Request $request)
        {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'cover_image' => 'required|image',
                'pinned' => 'required|boolean',
                'tags' => 'required|array',
            ]);

            $post = new Post($validated);
            $post->user_id = auth()->id();
            $post->cover_image = $request->file('cover_image')->store('images');
            $post->save();

            $post->tags()->attach($request->tags);

            return new PostResource($post);
        }


        public function destroy(Post $post)
        {
            if ($post->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $post->delete();

            return response()->json(['message' => 'Post deleted']);
        }


        public function viewDeleted()
        {
            $deletedPosts = Post::onlyTrashed()
                ->where('user_id', auth()->id())
                ->get();

            return PostResource::collection($deletedPosts);
        }


        public function restore($id)
        {
            $post = Post::onlyTrashed()->findOrFail($id);

            if ($post->user_id !== auth()->id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $post->restore();

            return new PostResource($post);
        }
    }
