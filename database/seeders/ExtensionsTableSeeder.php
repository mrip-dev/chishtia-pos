<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExtensionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('extensions')->updateOrInsert(
            ['id' => 2],
            [
                'act' => 'google-recaptcha2',
                'name' => 'Google Recaptcha 2',
                'description' => 'Key location is shown bellow',
                'image' => 'recaptcha3.png',
                'script' => '<script src="https://www.google.com/recaptcha/api.js"></script><div class="g-recaptcha" data-sitekey="{{site_key}}" data-callback="verifyCaptcha"></div><div id="g-recaptcha-error"></div>',
                'shortcode' => '{"site_key":{"title":"Site Key","value":"------------------"},"secret_key":{"title":"Secret Key","value":"------------"}}',
                'support' => 'recaptcha.png',
                'status' => 0,
                'created_at' => '2019-10-18 11:16:05',
                'updated_at' => '2024-05-08 03:23:13',
            ]
        );

        DB::table('extensions')->updateOrInsert(
            ['id' => 3],
            [
                'act' => 'custom-captcha',
                'name' => 'Custom Captcha',
                'description' => 'Just put any random string',
                'image' => 'customcaptcha.png',
                'script' => null,
                'shortcode' => '{"random_key":{"title":"Random String","value":"SecureString"}}',
                'support' => 'na',
                'status' => 0,
                'created_at' => '2019-10-18 11:16:05',
                'updated_at' => '2024-07-11 00:25:55',
            ]
        );
    }
}
