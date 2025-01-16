<?php

namespace Database\Seeders;

use App\Model\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = Config::where('id', 1)->first();
        if (!isset($settings)) {
            $directory = public_path() . '/uploads/config';
            if (is_dir($directory) != true) {
                \File::makeDirectory($directory, $mode = 0755, true);
            }
            \File::copy(
                public_path('images/logo.png'),
                public_path('uploads/config/cms_logo.png')
            );
            Config::create([
                'label' => 'cms logo',
                'type' => 'file',
                'value' => 'cms_logo.png',
            ]);
        }

        $settings2 = Config::where('id', 2)->first();
        if (!isset($settings2)) {
            Config::create([
                'label' => 'cms title',
                'type' => 'text',
                'value' => 'Cms',
            ]);
        }

        $settings3 = Config::where('id', 3)->first();
        if (!isset($settings3)) {
            Config::create([
                'label' => 'cms theme color',
                'type' => 'text',
                'value' => '#292961',
            ]);
        }
    }
}
