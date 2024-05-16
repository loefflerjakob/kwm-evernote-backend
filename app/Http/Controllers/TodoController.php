<?php

namespace App\Http\Controllers;


use App\Models\Kwmlist;
use App\Models\Todo;
use App\Models\User;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    public function index(): JsonResponse
    {
        $todos = Todo::with(['tags', 'users', 'note'])
            ->get();
        return response()->json($todos, 200);
    }

    public function findById(string $id): JsonResponse
    {
        $todo = Todo::where('id', $id)
            ->with(['tags', 'users', 'note'])->first();
        return $todo != null ? response()->json($todo, 200) : response()->json(null, 404);
    }

    public function save(Request $request)
    {
        $request = $this->parseRequest($request);


        DB::beginTransaction();
        try {
            $todo = Todo::create($request->all());

            $tag_ids = [];
            if (isset($request['tags']) && is_array($request['tags'])) {
                foreach ($request['tags'] as $t) {
                    array_push($tag_ids, $t['id']);
                }
            }

            $todo->tags()->sync($tag_ids);
            $todo->save();


            //add users
            $user_ids = [];
            if (isset($request['users']) && is_array($request['users'])) {
                foreach ($request['users'] as $u) {
                    array_push($user_ids, $u['id']);
                }
                $todo->is_shared = true;
            } else $todo->is_shared = false;

            $todo->users()->sync($user_ids);
            $todo->save();

            DB::commit();

            return response()->json($todo, 201);

        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json("saving kwmlist failed: " . $e->getMessage(), 420);
        }
    }



    public function update(Request $request, string $id)
    {
        $request = $this->parseRequest($request);


        DB::beginTransaction();
        try {
            $todo = Todo::with('tags', 'users')->
                where('id', $id)->first();

            if($todo != null) {
                $todo->update($request->all());


            //update tags
            $tag_ids = [];
            if (isset($request['tags']) && is_array($request['tags'])) {
                foreach ($request['tags'] as $t) {
                    array_push($tag_ids, $t['id']);
                }
            }

            $todo->tags()->sync($tag_ids);
            $todo->save();


            //update users
            $user_ids = [];
            if (isset($request['users']) && is_array($request['users'])) {
                foreach ($request['users'] as $u) {
                    array_push($user_ids, $u['id']);
                }
                $todo->is_shared = true;
            } else $todo->is_shared = false;

            $todo->users()->sync($user_ids);
            $todo->save();

            }
            DB::commit();

            $todo_new = Todo::with('tags', 'users')->
                where('id', $id)->first();
            return response()->json($todo_new, 201);

        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json("updating kwmlist failed: " . $e->getMessage(), 420);
        }
    }






    public function delete(string $id)
    {
        $todo = Todo::where('id', $id)->first();
        if ($todo != null) {
            $todo->delete();
            return response()->json("todo (' . $id . ') successfully deleted", 200);
        } else {
            return response()->json("todo (' . $id . ') does not exist", 422);
        }
    }






    private function parseRequest (Request $request) : Request {
        $date = new \DateTime($request->published);
        $request['due_date'] = $date->format('Y-m-d H:i:s');
        return $request;
    }


}
