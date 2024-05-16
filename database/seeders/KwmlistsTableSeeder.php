<?php

namespace Database\Seeders;

use App\Models\Kwmlist;
use App\Models\Note;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class KwmlistsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kwmlist = new Kwmlist;
        $kwmlist->name = 'Meine erste Liste cool';
        $kwmlist->is_shared = false;
        $kwmlist->save();

        $note1 = new Note;
        $note1->title = 'Coole Notiz';
        $note1->text = 'Notiz Text';
        $note1->image_url = 'https://picsum.photos/200/300';

        $note2 = new Note;
        $note2->title = 'Coole Notiz';
        $note2->text = 'Notiz Text';
        $note2->image_url = 'https://picsum.photos/200/300';

        $kwmlist->notes()->saveMany([$note1, $note2]);
        $kwmlist->save();

        $users = \App\Models\User::all()->pluck('id');
        $kwmlist->users()->sync($users);

        $kwmlist->save();

    }
}
