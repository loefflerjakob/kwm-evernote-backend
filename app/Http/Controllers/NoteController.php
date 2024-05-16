<?php

namespace App\Http\Controllers;


use App\Models\Kwmlist;
use App\Models\Todo;
use App\Models\User;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    public function index(): JsonResponse
    {
        $notes = Note::with(['kwmlist', 'todos', 'tags'])
            ->get();
        return response()->json($notes, 200);
    }

    public function findById(string $id): JsonResponse
    {
        $note = Note::where('id', $id)
            ->with(['kwmlist', 'todos', 'tags'])->first();
        return $note != null ? response()->json($note, 200) : response()->json(null, 404);
    }

    public function getTodosByNote(string $note_id): JsonResponse
    {
        $note = Note::findOrFail($note_id);
        $todos = $note->todos()->with(['tags', 'users'])->get();
        return $todos != null ? response()->json($todos, 200) : response()->json(null, 404);
    }


    public function save(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $note = Note::create($request->all());

            //add tags
            $tag_ids = [];
            if (isset($request['tags']) && is_array($request['tags'])) {
                foreach ($request['tags'] as $t) {
                    array_push($tag_ids, $t['id']);
                }
            }

            $note->tags()->sync($tag_ids);
            $note->save();


            //add todos
            $todoIds = [];
            $note->todos()->delete();
            if (isset($request['todos']) && is_array($request['todos'])) {
                foreach ($request['todos'] as $t) {
                    $todoIds[] = $t['id'];
                }
            }
            if (!empty($todoIds)) {
                $todos = Todo::findMany($todoIds);
                $note->todos()->saveMany($todos);
            }
            $note->save();

            DB::commit();

            return response()->json($note, 201);


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json("saving note failed: " . $e->getMessage(), 420);

        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $note = Note::with('tags', 'todos')->
            where('id', $id)->first();


            if ($note != null) {
                $note->update($request->all());


                //update tags
                $tag_ids = [];
                if (isset($request['tags']) && is_array($request['tags'])) {
                    foreach ($request['tags'] as $t) {
                        array_push($tag_ids, $t['id']);
                    }
                }
                $note->tags()->sync($tag_ids);
                $note->save();

                //update todos -> Hilfe von ChatGPT
                $note->todos()->update(['note_id' => null]);

                if (isset($request['todos']) && is_array($request['todos'])) {
                    $todoIds = array_column($request['todos'], 'id');
                    $todos = Todo::findMany($todoIds);
                    $note->todos()->saveMany($todos);
                }
                $note->save();

            }
            DB::commit();

            $note_new = Note::with('tags', 'todos')->
            where('id', $id)->first();
            return response()->json($note_new, 201);


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json("updating note failed: " . $e->getMessage(), 420);

        }
    }


    public function delete(string $id)
    {
        $notes = Note::where('id', $id)->first();
        if ($notes != null) {
            $notes->delete();
            return response()->json("note (' . $id . ') successfully deleted", 200);
        } else {
            return response()->json("note (' . $id . ') does not exist", 422);
        }
    }


}
