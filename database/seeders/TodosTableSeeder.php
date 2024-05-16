<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TodosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $todo = new Todo;
        $todo->title = 'Mein zweites Todo';
        $todo->text = 'Cooler todo Text';
        $todo->due_date = '2025-04-16 00:00:00';
        $todo->is_shared = false;
        $todo->note_id = 1;
        $todo->image_url = 'https://picsum.photos/200/300';
        $todo->save();

    }
}
