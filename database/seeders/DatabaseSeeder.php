<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Card;
use App\Models\Column;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Nonstandard\Uuid;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::insert(
            [
                [
                    'name' => 'William Moraes',
                    'email' => 'will.moraes.96@gmail.com',
                    'password' => Hash::make('test'),
                    'access_token' => '7696b09a-1522-4931-b57e-46ec37edb95f'
                ]
            ]
        );

        Board::insert(
            [
                [
                    'title' => 'My Board',
                    'user_id' => 1
                ]
            ]
        );

        Column::insert(
            [
                [
                    'order' => 1,
                    'title' => 'TO DO',
                    'board_id' => 1,
                    'user_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'order' => 2,
                    'title' => 'DOING',
                    'board_id' => 1,
                    'user_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'order' => 3,
                    'title' => 'DONE',
                    'board_id' => 1,
                    'user_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]
        );

        Card::insert(
            [
                [
                    'order' => 1,
                    'title' => 'Work on something',
                    'column_id' => 1,
                    'board_id' => 1,
                    'user_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'order' => 2,
                    'title' => 'Work on something else',
                    'column_id' => 1,
                    'board_id' => 1,
                    'user_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'order' => 1,
                    'title' => "I'm working on this",
                    'column_id' => 2,
                    'board_id' => 1,
                    'user_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'order' => 1,
                    'title' => 'I already worked on this',
                    'column_id' => 3,
                    'board_id' => 1,
                    'user_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]
        );
    }
}
