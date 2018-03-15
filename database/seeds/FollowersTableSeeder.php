<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = User::all();
        $user = $users->first();
        $userId = $user->id;

        $followers = $users->slice(1);
        $followerIds = $followers->pluck('id')->toArray();

        $user->follow($followerIds);

        foreach ($followers as $follower) {
            $follower->follow($userId);
        }
    }
}
