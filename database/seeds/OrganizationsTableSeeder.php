<?php

use Illuminate\Database\Seeder;
use alexeydg\Transliterate\TransliterationFacade;
use Carbon\Carbon;

class OrganizationsTableSeeder extends Seeder
{
    private $organizations;

    public function __construct()
    {
        $this->organizations = collect([
            ['title' => 'ООО Ромашечка']
        ]);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->organizations as $organization) {

            $code = Carbon::now()->timestamp
                . Transliterate::make($organization['title'], ['type' => 'url', 'lowercase' => true]);

            DB::table('organizations')->insert([
                'code' => md5($code),
                'title' => $organization['title'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
