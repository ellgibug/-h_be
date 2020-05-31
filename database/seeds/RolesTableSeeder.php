<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RolesTableSeeder extends Seeder
{
    private $roles;

    public function __construct()
    {
        $this->roles = collect([
            ['title' => 'Рут', 'value' => 'root'],
            ['title' => 'Администратор', 'value' => 'admin'],
            ['title' => 'Пользователь', 'value' => 'user'],
        ]);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->roles as $role) {

            DB::table('roles')->insert([
                'title' => $role['title'],
                'value' => $role['value'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
