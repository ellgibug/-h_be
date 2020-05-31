<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class UsersTableSeeder extends Seeder
{
    private $users;

    public function __construct()
    {
        $this->users = collect([
            [
                'name' => 'Ell Gibug',
                'email' => 'ellgibug@gmail.com',
                'password' => 'password',
                'organization_id' => '1',
                'role_id' => '1',
            ],
            [
                'name' => 'John Doe',
                'email' => 'johndoe@gmail.com',
                'password' => 'password',
                'organization_id' => '1',
                'role_id' => '2',
            ],
            [
                'name' => 'Simple User',
                'email' => 'user@gmail.com',
                'password' => 'password',
                'organization_id' => '1',
                'role_id' => '3',
            ]
        ]);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->users as $user) {

            $code = Carbon::now()->timestamp . $user['email'];

            DB::table('users')->insert([
                'name' => $user['name'],
                'code' => $code,
                'email' => $user['email'],
                'password' => bcrypt( $user['password']),
                'organization_id' => $user['organization_id'],
                'role_id' => $user['role_id'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
