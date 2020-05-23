<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use alexeydg\Transliterate\TransliterationFacade;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $title_1 = 'Lorem inpsum';
        $title_2 = 'Съешь ещё этих мягких французских булок, да выпей чаю';

        DB::table('pages')->insert([
            'project_id' => 1,
            'title' => $title_1,
            'url' => Transliterate::make($title_1, ['type' => 'url', 'lowercase' => true]),
            'is_published' => true,
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam semper vel nisi ut commodo. Nulla facilisi. In pretium lectus efficitur, iaculis ipsum eget, posuere turpis. Etiam iaculis lectus quis arcu ornare, ac accumsan metus pellentesque. Nulla euismod quis tortor ut mollis. Sed gravida dapibus augue non efficitur. Donec non justo a tellus vehicula imperdiet. Sed pharetra semper volutpat.

Proin in turpis orci. Mauris dictum lorem ac sem accumsan euismod in venenatis est. Mauris ut vulputate justo. Phasellus vel porttitor turpis. Nam id purus eu nulla bibendum efficitur nec eu felis. Sed nec nunc est. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin dictum eros in massa sollicitudin, vitae feugiat magna suscipit. Nullam malesuada mattis aliquam. Proin lacinia risus at magna tincidunt, ut lacinia quam malesuada. Interdum et malesuada fames ac ante ipsum primis in faucibus. Maecenas eu commodo odio.

Morbi vitae posuere metus, vel posuere libero. Nulla eget porttitor odio. Nulla sed elit libero. Sed nec ante nec massa convallis cursus eget ac eros. Vestibulum varius vulputate mauris, sed lacinia lacus tempus et. Ut tempus imperdiet lorem, ac maximus erat ullamcorper et. Donec a ipsum rhoncus velit tempor laoreet. Morbi scelerisque lacus vitae dui auctor gravida. In ultrices ante dolor, ac faucibus magna cursus et.'
        ]);

        DB::table('pages')->insert([
            'project_id' => 1,
            'title' => $title_2,
            'url' => Transliterate::make($title_2, ['type' => 'url', 'lowercase' => true]),
            'is_published' => true,
            'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam semper vel nisi ut commodo. Nulla facilisi. In pretium lectus efficitur, iaculis ipsum eget, posuere turpis. Etiam iaculis lectus quis arcu ornare, ac accumsan metus pellentesque. Nulla euismod quis tortor ut mollis. Sed gravida dapibus augue non efficitur. Donec non justo a tellus vehicula imperdiet. Sed pharetra semper volutpat.

Proin in turpis orci. Mauris dictum lorem ac sem accumsan euismod in venenatis est. Mauris ut vulputate justo. Phasellus vel porttitor turpis. Nam id purus eu nulla bibendum efficitur nec eu felis. Sed nec nunc est. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin dictum eros in massa sollicitudin, vitae feugiat magna suscipit. Nullam malesuada mattis aliquam. Proin lacinia risus at magna tincidunt, ut lacinia quam malesuada. Interdum et malesuada fames ac ante ipsum primis in faucibus. Maecenas eu commodo odio.

Morbi vitae posuere metus, vel posuere libero. Nulla eget porttitor odio. Nulla sed elit libero. Sed nec ante nec massa convallis cursus eget ac eros. Vestibulum varius vulputate mauris, sed lacinia lacus tempus et. Ut tempus imperdiet lorem, ac maximus erat ullamcorper et. Donec a ipsum rhoncus velit tempor laoreet. Morbi scelerisque lacus vitae dui auctor gravida. In ultrices ante dolor, ac faucibus magna cursus et.'
        ]);
    }
}
