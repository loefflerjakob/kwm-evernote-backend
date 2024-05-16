<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Todo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class NotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $note = new Note;
        $note->title = 'Meine erste Notiz';
        $note->text = 'Cooler Notiz Teeext';
        $note->image_url = 'https://picsum.photos/200/300';
        $note->kwmlist_id = 1;
        $note->save();

        $todo1 = new Todo;
        $todo1->title = 'Todo Titel';
        $todo1->text = 'Cooler todo Text';
        $todo1->due_date = '2025-04-16 00:00:00';
        $todo1->is_shared = false;
        $todo1->note_id = 1;
        $todo1->image_url = 'https://picsum.photos/200/300';


        $note->todos()->save($todo1);

    }
}
