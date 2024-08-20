<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use DB;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // php artisan db:seed --class=PassportSeeder
        \Artisan::call('passport:install');
        // \Artisan::call('passport:client --name=app --no-interaction --personal');
    }
}