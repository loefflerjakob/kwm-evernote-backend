<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User;
        $user->firstName = 'Martina';
        $user->lastName = 'Sterl';
        $user->email = 'sterl@mail.com';
        $user->password = bcrypt('coolesPW');
        $user->image_url = 'https://picsum.photos/200';
        $user->save();

        $user2 = new User;
        $user2->firstName = 'Jakob';
        $user2->lastName = 'Löffler';
        $user2->email = 'löffler@mail.com';
        $user2->password = bcrypt('coolesPW');
        $user2->image_url = 'https://picsum.photos/200';
        $user2->save();
    }
}
