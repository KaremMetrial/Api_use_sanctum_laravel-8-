<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Http\Resources\TagResource;
    use App\Models\Tag;
    use Illuminate\Http\Request;

    class TagController extends Controller
    {
        public function index()
        {
            $tags = Tag::all();
            return TagResource::collection($tags);
        }

        public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255|unique:tags,name'
            ]);
            $tag = Tag::create($request->only('name'));
            return new TagResource($tag);
        }

        public function create()
        {

        }

        public function show(Tag $tag)
        {
            return new TagResource($tag);
        }

        public function edit($id)
        {
            //
        }

        public function update(Request $request, Tag $tag)
        {
            $request->validate([
                'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            ]);
            $tag->update($request->only('name'));
            return new TagResource($tag);
        }

        public function destroy(Tag $tag)
        {
            $tag->delete();
            return response()->json(null,204);
        }
    }
