<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('posts')->insert([
            [
                'user_id' => 1,
                'body' => fake()->sentence,
                'image' => 'https://th.bing.com/th/id/OIP.IadWjX7Z2rp2w7Td5k-LAwHaFA?pid=ImgDet&rs=1',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'user_id' => 1,
                'body' => fake()->sentence,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
