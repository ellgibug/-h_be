<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use alexeydg\Transliterate\TransliterationFacade;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $title = 'Демонстрационный проект';

        DB::table('projects')->insert([
            'user_id' => 1,
            'title' => $title,
            'url' => Transliterate::make($title, ['type' => 'url', 'lowercase' => true]),
            'is_demo' => true,
            'is_published' => true
        ]);
    }
}
