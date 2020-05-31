<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use alexeydg\Transliterate\TransliterationFacade;
use Carbon\Carbon;


class ProjectsTableSeeder extends Seeder
{
    private $projects;

    public function __construct()
    {
        $this->projects = collect([
            [
                'user_id' => '2',
                'organization_id' => '1',
                'title' => 'Демонстрационный проект',
                'is_demo' => true,
                'is_published' => true,
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
        foreach ($this->projects as $project) {

            $code = Carbon::now()->timestamp
                . Transliterate::make($project['title'], ['type' => 'url', 'lowercase' => true]);

            DB::table('projects')->insert([
                'code' => md5($code),
                'title' => $project['title'],
                'url' => Transliterate::make($project['title'], ['type' => 'url', 'lowercase' => true]),
                'is_demo' => $project['is_demo'],
                'is_published' => $project['is_published'],
                'organization_id' => $project['organization_id'],
                'user_id' => $project['user_id'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
