<?php

namespace App\Http\Controllers;



use App\Models\Kwmlist;
use App\Models\User;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KwmlistController extends Controller
{
    public function index(): JsonResponse
    {
        $kwmlists = Kwmlist::with(['users', 'notes'])
            ->get();
        return response()->json($kwmlists, 200);
    }


    public function findById(string $id): JsonResponse
    {
        $kwmlist = Kwmlist::where('id', $id)
            ->with(['users', 'notes', 'users'])->first();
        return $kwmlist != null ? response()->json($kwmlist, 200) : response()->json(null, 404);
    }

    public function getNotesByKwmlist(string $kwmlist_id): JsonResponse
    {
        $kwmlist = Kwmlist::findOrFail($kwmlist_id);
        $notes = $kwmlist->notes()->with(['todos', 'tags', 'users'])->get();
        return $notes != null ? response()->json($notes, 200) : response()->json(null, 404);
    }






    public function save(Request $request) : JsonResponse {

        DB::beginTransaction();
        try {
            $kwmlist = Kwmlist::create($request->all());

            //add users
            $user_ids = [];
            if (isset($request['users']) && is_array($request['users'])) {
                foreach ($request['users'] as $u) {
                    array_push($user_ids, $u['id']);
                }
                $kwmlist->is_shared = true;
            } else $kwmlist->is_shared = false;

            $kwmlist->users()->sync($user_ids);
            $kwmlist->save();

            //add notes
            $noteIds = [];
            //$kwmlist->notes()->delete();
            if (isset($request['notes']) && is_array($request['notes'])) {
                foreach ($request['notes'] as $n) {
                    $noteIds[] = $n['id'];
                }
            }
            if (!empty($noteIds)) {
                $notes = Note::findMany($noteIds);
                $kwmlist->notes()->saveMany($notes);
            }
            $kwmlist->save();


            DB::commit();

            return response()->json($kwmlist, 201);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return response()->json("saving kwmlist failed: " . $e->getMessage(), 420);
        }
    }


    /*
     * Update kwmlist with given id
     * Users and Notes can be added by providing the id of user or note
     */
    public function update(Request $request, string $id) : JsonResponse {
        DB::beginTransaction();
        try {
            $kwmlist = Kwmlist::with('users', 'notes')->
                where('id', $id)->first();

            if($kwmlist != null) {
                $kwmlist->update($request->all());


                //update users
                $user_ids = [];
                if (isset($request['users']) && is_array($request['users'])) {
                    foreach ($request['users'] as $u) {
                        array_push($user_ids, $u['id']);
                    }
                    $kwmlist->is_shared = true;
                } else $kwmlist->is_shared = false;
                $kwmlist->users()->sync($user_ids);
                $kwmlist->save();


                //update notes
                $kwmlist->notes()->update(['kwmlist_id' => null]);

                if (isset($request['notes']) && is_array($request['notes'])) {
                    $noteIds = array_column($request['notes'], 'id');
                    $notes = Note::findMany($noteIds);
                    $kwmlist->notes()->saveMany($notes);
                }
                $kwmlist->save();

            }

            DB::commit();
            $kwmlist_new = Kwmlist::with('users', 'notes')->
            where('id', $id)->first();
            return response()->json($kwmlist_new, 201);


        } catch (\Exception $e){
        DB::rollback();
        return response()->json('updating kwmlist failed: ' . $e->getMessage(), 420);
        }
    }


    public function delete (string $id) {
        $kwmlist = Kwmlist::where('id', $id)->first();
        if ($kwmlist != null) {
            $kwmlist->delete();
            return response()->json("kwmlist (' . $id . ') successfully deleted", 200);
        }
        else {
            return response()->json("kwmlist (' . $id . ') does not exist", 422);
        }
    }



}
