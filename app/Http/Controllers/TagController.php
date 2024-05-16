<?php

namespace App\Http\Controllers;


use App\Models\Kwmlist;
use App\Models\Tag;
use App\Models\User;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $tags = Tag::with(['notes', 'todos'])
            ->get();
        return response()->json($tags, 200);
    }


    public function findById(string $id): JsonResponse
    {
        $tags = Tag::where('id', $id)
            ->with(['notes', 'todos'])->first();
        return $tags != null ? response()->json($tags, 200) : response()->json(null, 404);
    }

    public function save(Request $request): JsonResponse
    {

        DB::beginTransaction();
        try {
            $tag = Tag::create($request->all());
            $tag->save();

            DB::commit();

            return response()->json($tag, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json("saving tag failed: " . $e->getMessage(), 420);
        }
    }


    public function update(Request $request, string $id): JsonResponse
    {

        DB::beginTransaction();
        try {
            $tag = Tag::with('notes', 'todos')->
            where('id', $id)->first();

            if ($tag != null) {
                $tag->update($request->all());


            //update notes
            $note_ids = [];
            if (isset($request['notes']) && is_array($request['notes'])) {
                foreach ($request['notes'] as $n) {
                    array_push($note_ids, $n['id']);
                }
                $tag->notes()->sync($note_ids);
                $tag->save();
            }

            //update todos
            $todo_ids = [];
            if (isset($request['todos']) && is_array($request['todos'])) {
                foreach ($request['todos'] as $t) {
                    array_push($todo_ids, $t['id']);
                }
                $tag->todos()->sync($todo_ids);
                $tag->save();
            }

            }
            DB::commit();
            $tag_new = Tag::with('todos', 'notes')->
            where('id', $id)->first();
            return response()->json($tag_new, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json("saving tag failed: " . $e->getMessage(), 420);
        }
    }


    public function delete (string $id) {
        $tag = Tag::where('id', $id)->first();
        if ($tag != null) {
            $tag->delete();
            return response()->json("tag (' . $id . ') successfully deleted", 200);
        }
        else {
            return response()->json("tag (' . $id . ') does not exist", 422);
        }
    }
}

